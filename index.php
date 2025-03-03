<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peru Consultas</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="sweetalert2.min.css">

</head>

<body>
    <div class="demo-page">
        <div class="demo-page-navigation">
            <nav>
                <ul>
                    <li>
                        <a href="#searchDNI">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Buscar DNI
                        </a>
                    </li>
                    <li>
                        <a href="#searchRUC">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            Buscar RUC
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
        <main class="demo-page-content">
            <section>
                <div class="href-target" id="searchDNI"></div>
                <h1 class="package-name">Buscar por DNI</h1>
                <div class="nice-form-group">
                    <label>Buscar</label>
                    <input type="search" placeholder="Buscar por DNI" value="" id="txt_dni" maxlength="8" />
                </div>
                <br>
                <h2>
                    <code id="code_dni"></code>
                </h2>
            </section>
            <section>
                <div class="href-target" id="searchRUC"></div>
                <h1 class="package-name">Buscar por RUC</h1>
                <div class="nice-form-group">
                    <label>Buscar</label>
                    <input type="search" placeholder="Buscar por RUC" value="" id="txt_ruc" maxlength="11" />
                </div>
                <br>
                <h2>
                    <code id="code_ruc"></code>
                </h2>
            </section>
        </main>
    </div>
    <script src="jquery-3.7.1.min.js"></script>
    <script src="jquery.blockUI.js"></script>
    <script src="sweetalert2.min.js"></script>
    <script>
        $(() => {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            const inputDNI = document.getElementById("txt_dni");
            const codeDNI = document.getElementById("code_dni");

            inputDNI.addEventListener('input', function(e) {
                if (inputDNI.value.length === 8) {
                    $.blockUI({
                        message: '<div class="lds-facebook"><div></div><div></div><div></div><div></div></div>'
                    });
                    const dni = inputDNI.value;
                    fetch('dni.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                'dni': dni
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            $.unblockUI();
                            if (data.success) {
                                const name = data.nombre_completo;
                                console.log(data);
                                codeDNI.innerText = name;

                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(name)
                                        .then(() => {
                                            const successSound = new Audio('success.mp3');
                                            successSound.play();
                                            Toast.fire({
                                                icon: "success",
                                                title: "Nombres copiados"
                                            });
                                        })
                                        .catch(err => {
                                            console.error('Error al copiar al portapapeles:', err);
                                            copyToClipboard(name); // Usa la alternativa si falla
                                        });
                                } else {
                                    console.warn("Clipboard API no disponible. Usando alternativa.");
                                    copyToClipboard(name);
                                }
                            } else if (data.error) {
                                codeDNI.innerText = "";
                                Toast.fire({
                                    icon: "error",
                                    title: data.error
                                });
                            } else {
                                codeDNI.innerText = "";
                                Toast.fire({
                                    icon: "error",
                                    title: "DNI no existe"
                                });
                            }
                        })
                        .catch(err => {
                            $.unblockUI();
                            console.error('Error al obtener los datos:', err);
                            Toast.fire({
                                icon: "error",
                                title: "Error al obtener los datos"
                            });
                        });
                } else {
                    codeDNI.innerText = "";
                }
            });

            const inputRUC = document.getElementById("txt_ruc");
            const codeRUC = document.getElementById("code_ruc");

            inputRUC.addEventListener('input', function(e) {
                if (inputRUC.value.length === 11) {
                    $.blockUI({
                        message: '<div class="lds-facebook"><div></div><div></div><div></div><div></div></div>'
                    });
                    const ruc = inputRUC.value;

                    fetch('ruc.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                'ruc': ruc
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            $.unblockUI();
                            if (data.success) {
                                const nameRUC = data.razon_social;
                                const estado = data.estado;
                                if (estado === 'ACTIVO') {
                                    codeRUC.innerText = nameRUC;

                                    if (navigator.clipboard && navigator.clipboard.writeText) {
                                        navigator.clipboard.writeText(nameRUC)
                                            .then(() => {
                                                const successSound = new Audio('success.mp3');
                                                successSound.play();
                                                Toast.fire({
                                                    icon: "success",
                                                    title: "Razón social copiada"
                                                });
                                            })
                                            .catch(err => {
                                                console.error('Error al copiar al portapapeles:', err);
                                                copyToClipboardRuc(nameRUC); // Usa la alternativa si falla
                                            });
                                    } else {
                                        console.warn("Clipboard API no disponible. Usando alternativa.");
                                        copyToClipboardRuc(nameRUC);
                                    }
                                } else {
                                    codeRUC.innerText = "RUC inactivo";
                                    Toast.fire({
                                        icon: "error",
                                        title: "No se ha encontrado razón social"
                                    });
                                }
                            } else if (data.error) {
                                codeRUC.innerText = "";
                                Toast.fire({
                                    icon: "error",
                                    title: data.error
                                });
                            } else {
                                codeRUC.innerText = "";
                                Toast.fire({
                                    icon: "error",
                                    title: "RUC no existe"
                                });
                            }
                        })
                        .catch(err => {
                            $.unblockUI();
                            console.error('Error al obtener los datos:', err);
                            Toast.fire({
                                icon: "error",
                                title: "Error al obtener los datos"
                            });
                        });
                } else {
                    codeRUC.innerText = "";
                }
            });

            function copyToClipboard(text) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);

                // Reproducir sonido de éxito
                const successSound = new Audio('success.mp3');
                successSound.play();
                Toast.fire({
                    icon: "success",
                    title: "Nombres copiados"
                });
            }

            function copyToClipboardRuc(text) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);

                // Reproducir sonido de éxito
                const successSound = new Audio('success.mp3');
                successSound.play();

                Toast.fire({
                    icon: "success",
                    title: "Razón social copiada"
                });
            }
        });
    </script>
</body>

</html>