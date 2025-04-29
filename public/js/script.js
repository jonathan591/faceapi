let labeledFaceDescriptors = [];
let modelsLoaded = false;
let descriptorsCache = {};
let loadedUsers = new Set();

// Mostrar mensaje de carga
function showLoadingMessage(show) {
    const loadingMessage = document.getElementById('loading-message');
    loadingMessage.style.display = show ? 'block' : 'none';
}

async function loadModels() {
    const MODEL_URL = '/models'; // Cambia esto a la ruta correcta de tus modelos
    await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    modelsLoaded = true;
    console.log("Modelos cargados");
}
window.onload = async function() {
    showLoadingMessage(true);
    await loadModels();                
    await loadLabeledImagesAsync();    
    showLoadingMessage(false);
    document.getElementById('start-camera').disabled = false; // Activamos el botón
};


async function processBatch(batch) {
    await Promise.all(
        batch.map(async (user) => {
            if (loadedUsers.has(user.cedula)) return;
            loadedUsers.add(user.cedula);

            try {
                const response = await fetch(`/api/images?name=${user.cedula}`);
                const blob = await response.blob();
                const img = await faceapi.bufferToImage(blob);
               
                if (!img) return console.error(`No se pudo cargar imagen para: ${user.cedula}`);

                const detections = await faceapi.detectSingleFace(img)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
            
                if (detections?.descriptor) {
                    const labeledDescriptor = new faceapi.LabeledFaceDescriptors(
                        user.cedula,  // Usamos la cédula como identificador
                        [detections.descriptor]
                    );

                
                    
                    if (!labeledFaceDescriptors.some(d => d.label === user.cedula)) {
                        labeledFaceDescriptors.push(labeledDescriptor);
                        descriptorsCache[user.cedula] = {
                            descriptor: labeledDescriptor,
                            nombre: user.nombre  // Guardamos el nombre en caché
                        };
                    }
                }
            } catch (error) {
                console.error(`Error procesando ${user.cedula}:`, error);
            }
        })
    );
}
async function loadLabeledImagesAsync() {
    // Mostrar el mensaje de carga
    showLoadingMessage(true);

    try {
       
        const response = await fetch('/api/get-labels');
        const { labels, totalUsers } = await response.json();

        // Limpiar el array antes de cargar nuevos descriptores
        labeledFaceDescriptors = [];

        // Procesar usuarios en lotes pequeños para evitar sobrecargar la memoria
        const batchSize = 10; // Tamaño del lote
        for (let i = 0; i < labels.length; i += batchSize) {
            const batch = labels.slice(i, i + batchSize); // Obtener el siguiente lote
            await processBatch(batch);
        }

        console.log("Descriptores cargados:", labeledFaceDescriptors);
    } catch (error) {
        console.error("Error al cargar los descriptores desde la base de datos:", error);
    } finally {
        // Ocultar el mensaje de carga
        showLoadingMessage(false);
    }
}



document.getElementById('start-camera').addEventListener('click', startCamera);
async function startCamera() {
    if (!modelsLoaded) return console.error("Modelos no cargados.");

    const video = document.getElementById('video');
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: {} })
            .then(stream => {
                video.srcObject = stream;
                video.play();
            })
            .catch(err => console.error("Error al activar la cámara: ", err));
    }

    video.addEventListener('loadeddata', async () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        Object.assign(canvas.style, { position: 'absolute', top: 0, left: 0, width: '100%', height: '100%' });
        document.getElementById('camera').appendChild(canvas);

        const updateCanvasSize = () => {
            faceapi.matchDimensions(canvas, { width: video.clientWidth, height: video.clientHeight });
        };

        updateCanvasSize();
        window.addEventListener('resize', updateCanvasSize);

        setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
            const displaySize = { width: video.clientWidth, height: video.clientHeight };
            const resized = faceapi.resizeResults(detections, displaySize);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            faceapi.draw.drawDetections(canvas, resized);
            faceapi.draw.drawFaceLandmarks(canvas, resized);

            if (labeledFaceDescriptors.length > 0) {
                const matcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.5);
                const results = resized.map(d => matcher.findBestMatch(d.descriptor));

                results.forEach(async (result, i) => {
                    const box = resized[i].detection.box;
                    const isUnknown = result.label === 'unknown';
                    const color = isUnknown ? 'red' : 'green';
                    
                    // 1. Obtener el nombre desde el cache usando la cédula (result.label)
                    const nombre = !isUnknown 
                        ? descriptorsCache[result.label]?.nombre 
                        : 'Desconocido';
                    
                    // 2. Calcular porcentaje de confianza (ej: 95%)
                    const porcentaje = Math.round((1 - result.distance) * 100);
                    
                    // 3. Mostrar SOLO el nombre + porcentaje (ej: "Juan Pérez (95%)")
                    const displayText = isUnknown 
                        ? 'Desconocido' 
                        : `${nombre} (${porcentaje}%)`;
                    
                    // 4. Dibujar el cuadro
                    new faceapi.draw.DrawBox(box, { 
                        label: displayText,
                        boxColor: color
                    }).draw(canvas);

                    if (result.label === 'unknown') {
                        notifyUser('Usuario no encontrado', true);
                    } else if (result.distance < 0.5) {
                        const userId = await getUserIdByName(result.label);
                        console.log("ID de usuario:", userId);
                        if (userId) {
                           
                            let response = await fetch('/api/horarios');
                            let data = await response.json();

                            let now = new Date();
                            let currentHours = now.getHours();
                            let currentMinutes = now.getMinutes();

                            function isBetween(currentH, currentM, startH, startM, endH, endM) {
                                const currentTotal = currentH * 60 + currentM;
                                const startTotal = startH * 60 + startM;
                                const endTotal = endH * 60 + endM;
                                return currentTotal >= startTotal && currentTotal <= endTotal;
                            }

                            let success = null;

                                for (let horario of data) {
                                    let [startHour, startMin] = horario.desde.split(':').map(Number);
                                    let [endHour, endMin] = horario.hasta.split(':').map(Number);

                                    if (isBetween(currentHours, currentMinutes, startHour, startMin, endHour, endMin)) {
                                        if (horario.accion === 'Entrada') {
                                            success = await registerEntry(userId);
                                        } else if (horario.accion === 'Salida') {
                                            success = await registerExit(userId);
                                        }
                                        break; // Ya encontramos el horario correcto, no seguimos buscando
                                    }
                                }

                                if (success === null) {
                                    console.log("No es hora válida para registrar entrada o salida.");
                                     notifyUser('No es hora válida para registrar entrada o salida.');
                                    showCustomAlert('No es hora válida para registrar entrada o salida.');
                                }
                            // let success = now.getHours() < 11 || (now.getHours() === 11 && now.getMinutes() < 10)
                            //     ? await registerEntry(userId)
                            //     : await registerExit(userId);

                            // if (success) {
                            //     notifyUser(`Usuario ${result.label} registrado exitosamente`);
                            //     showCustomAlert(`Registro de ${result.label} exitoso`);
                            // }
                        }
                    }
                });
            }
        }, 5000);
    });
}

function showCustomAlert(message) {
    const alertBox = document.getElementById('custom-alert');
    alertBox.textContent = message;
    alertBox.style.display = 'block';
    setTimeout(() => alertBox.style.display = 'none', 4000);
}

function notifyUser(message, isError = false) {
    const result = document.getElementById('recognition-result');
    Object.assign(result.style, {
        display: 'block',
        color: isError ? 'red' : 'green',
        fontWeight: 'bold',
        fontSize: '20px',
        backgroundColor: isError ? '#ffcccc' : '#ccffcc',
        padding: '10px',
        borderRadius: '5px',
        border: `2px solid ${isError ? 'red' : 'green'}`
    });
    result.textContent = message;
}

async function registerEntry(userId) {
    try {
        const checkResponse = await fetch(`/api/checkentrada?usuarioId=${userId}`);
        const result = await checkResponse.json();
        if (result.entryExists) {
            notifyUser('Ya se ha registrado una entrada hoy.');
            // console.log('ya',result.entryExists);
            return false;
        }

        const response = await fetch('/api/registroentrado', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ usuarioId: userId })
        });
         // Agregado para depuración

        if (response.ok) {
            notifyUser('Entrada registrada exitosamente.');
            showCustomAlert('Entrada registrada exitosamente.');
            return true;
        } else if (response.status === 409) {
            notifyUser('Ya se ha registrado una entrada hoy.', true);
        } else {
            notifyUser('Error al registrar la entrada.', true);
           
        }
    } catch (err) {
        console.error('Error al registrar entrada:', err);
  
        notifyUser('Error de conexión.', true);
    }
    return false;
}

async function registerExit(userId) {
    try {
        const checkResponse = await fetch(`/api/checksalida?usuarioId=${userId}`);
        const result = await checkResponse.json();
        if (result.entryExists) {
            notifyUser('Ya se ha registrado una salida hoy.');
            // console.log('ya',result.entryExists);
            return false;
        }

        const response = await fetch('/api/registrosalida', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ usuarioId: userId })
        });

        if (response.ok) {
            notifyUser('Salida registrada exitosamente.');
            showCustomAlert('Salida registrada exitosamente.');
            return true;
        } else if (response.status === 409) {
            notifyUser('Ya se ha registrado una salida hoy.', true);
        } else {
            notifyUser('Error al registrar la salida.', true);
        }
    } catch (err) {
        console.error('Error al registrar salida:', err);
        notifyUser('Error de conexión.', true);
    }
    return false;
}


async function getUserIdByName(name) {
    const response = await fetch(`/api/get-user-id?name=${name}`);
    if (response.ok) {
        const data = await response.json();
        return data.id;
    }
    return null;
}