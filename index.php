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
                    fetch(`https://consultaruc.win/api/dni/${dni}`)
                        .then(response => response.json())
                        .then(({
                            result
                        }) => {
                            if (result.Materno) {
                                const name = `${result.Nombres} ${result.Paterno} ${result.Materno}`;
                                codeDNI.innerText = name;

                                // Copiar el nombre al portapapeles
                                navigator.clipboard.writeText(name)
                                    .then(() => {
                                        $.unblockUI();
                                        // Reproducir sonido de éxito
                                        const successSound = new Audio('success.mp3');
                                        successSound.play();

                                        // Feedback al usuario
                                        Toast.fire({
                                            icon: "success",
                                            title: "Nombres Copiados"
                                        });
                                    })
                                    .catch(err => {
                                        $.unblockUI();

                                        console.error('Error al copiar al portapapeles:', err);
                                    });
                            } else {
                                codeDNI.innerText = "";
                                $.unblockUI();

                                // Feedback al usuario
                                Toast.fire({
                                    icon: "error",
                                    title: "DNI no existe"
                                });
                            }
                        })
                        .catch(() => {
                            console.error('Error al obtener los datos');
                        });
                } else {
                    codeDNI.innerText = "";
                }
            });


            const inputRUC = document.getElementById("txt_ruc");
            const codeRUC = document.getElementById("code_ruc");

            inputRUC.addEventListener('input', function(e) {
                if (inputRUC.value.length === 11) {
                    const ruc = inputRUC.value;
                    fetch(`https://consultaruc.win/api/ruc/${ruc}`)
                        .then(response => response.json())
                        .then(({
                            result
                        }) => {
                            const nameRUC = result.razon_social;
                            const estado = result.estado;
                            if (estado === 'ACTIVO') {
                                codeRUC.innerText = nameRUC;

                                // Copiar el texto al portapapeles
                                navigator.clipboard.writeText(nameRUC)
                                    .then(() => {
                                        // Reproducir sonido de éxito
                                        const successSound = new Audio('success.mp3');
                                        successSound.play();
                                        Toast.fire({
                                            icon: "success",
                                            title: "Razón social copiada"
                                        });
                                    })
                                    .catch(err => {
                                        console.error('Error al copiar al portapapeles:', err);
                                    });
                            } else {
                                codeRUC.innerText = "RUC inactivo";
                                Toast.fire({
                                    icon: "error",
                                    title: "No se ha encontrado razón social"
                                });
                            }
                        })
                        .catch(() => {
                            console.error('Error al obtener los datos');
                        });
                } else {
                    codeRUC.innerText = "";
                }
            });
        })
    </script>
</body>

</html>