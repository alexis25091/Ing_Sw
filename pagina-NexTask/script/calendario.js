document.addEventListener("DOMContentLoaded", () => {

    //  CALENDARIO  
    // Contenedor donde se dibujan los días
    const calendarGrid = document.getElementById("calendar-grid");

    // Texto donde se muestra el mes actual
    const monthLabel = document.getElementById("current-month-label");

    // Fecha base del calendario (hoy)
    let currentDate = new Date();

    // Función que genera el calendario
    function renderCalendar(date) {

        // Limpia el calendario antes de dibujar
        calendarGrid.innerHTML = "";

        const year = date.getFullYear();
        const month = date.getMonth();

        // Primer y último día del mes
        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        // La semana empiece en domingo
        let startDay = firstDayOfMonth.getDay();
        let endDay = lastDayOfMonth.getDay();

        // Días del mes anterior (para rellenar espacios)
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        // Total de celdas del calendario
        const totalDays = startDay + lastDayOfMonth.getDate() + (6 - endDay);

        // Nombres de los meses
        const monthNames = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

        // Mostrar mes y año actual
        monthLabel.textContent = `${monthNames[month]} ${year}`;

        // Crear cada día del calendario
        for(let i = 0; i < totalDays; i++) {

            const dayCell = document.createElement("div");
            dayCell.classList.add("calendar-day");

            let dayNumber, cellDate;

            // Días del mes anterior
            if(i < startDay) {
                dayNumber = daysInPrevMonth - (startDay - 1) + i;
                cellDate = new Date(year, month - 1, dayNumber);
                dayCell.classList.add("outside-month");

            // Días del siguiente mes
            } else if(i >= startDay + lastDayOfMonth.getDate()) {
                dayNumber = i - (startDay + lastDayOfMonth.getDate()) + 1;
                cellDate = new Date(year, month + 1, dayNumber);
                dayCell.classList.add("outside-month");

            // Días del mes actual
            } else {
                dayNumber = i - startDay + 1;
                cellDate = new Date(year, month, dayNumber);
            }

            // Para mostrar el dia actual
            const hoy = new Date();
            if (
                cellDate.getFullYear() === hoy.getFullYear() &&
                cellDate.getMonth() === hoy.getMonth() &&
                cellDate.getDate() === hoy.getDate()
            ) {
                dayCell.classList.add("hoy");
            }

            // Número del día
            const dayTitle = document.createElement("h4");
            dayTitle.textContent = dayNumber;
            dayCell.appendChild(dayTitle);

            //  MOSTRAR TAREAS DEL DÍA 
            if(cellDate.getMonth() === month) {

                // Filtra tareas que coincidan con la fecha
                const dayTareas = tareas.filter(t => {
                    const tFecha = new Date(t.fecha_limite);
                    return tFecha.getFullYear() === year &&
                           tFecha.getMonth() === month &&
                           tFecha.getDate() === dayNumber;
                });

                // Crear cada tarea
                dayTareas.forEach(t => {

                    const taskEl = document.createElement("div");
                    taskEl.classList.add("task-item");

                    // Nombre de la materia
                    taskEl.textContent = t.materia_nombre || "Sin materia asignada";

                    // Aplicar color dinámico
                    taskEl.style.background = t.color + "33"; // color con transparencia
                    taskEl.style.borderLeft = "5px solid " + t.color;
                    taskEl.style.color = "#000";

                    // Evento click → mostrar detalles en modal
                    taskEl.addEventListener("click", (e) => {
                        e.stopPropagation();

                        // Llenar datos del modal
                        document.getElementById("detalle-materia").textContent = t.materia_nombre || "Sin materia asignada";
                        document.getElementById("detalle-dificultad").textContent = t.dificultad;
                        document.getElementById("detalle-fecha").textContent = t.fecha_limite;

                        // Si no hay detalles, mostrar texto por defecto
                        document.getElementById("detalle-detalles").textContent = t.detalles || "Tarea sin detalles";

                        // Estado de la tarea
                        document.getElementById("detalle-estado").textContent = t.estado_legible || t.estado;

                        // Mostrar modal
                        document.getElementById("modal-detalle").classList.add("active");
                    });

                    // Agregar tarea al día
                    dayCell.appendChild(taskEl);
                });
            }

            // Agregar día al calendario
            calendarGrid.appendChild(dayCell);
        }
    }

    //  NAVEGACIÓN ENTRE MESES 
    document.getElementById("prev-month").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    document.getElementById("next-month").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    //  CERRAR MODAL ====
    document.getElementById("cerrar-modal-detalle").addEventListener("click", () => {
        document.getElementById("modal-detalle").classList.remove("active");
    });

    // Render inicial del calendario
    renderCalendar(currentDate);

    //  MENÚ USUARIO  
    const toggleUsuario = document.querySelector('.toggle-usuario');
    const submenu = document.getElementById('submenu-usuario');

    if(toggleUsuario && submenu){

        // Mostrar / ocultar menú
        toggleUsuario.addEventListener('click', () => {
            submenu.classList.toggle('active');
        });

        // Cerrar menú si se hace click fuera
        document.addEventListener('click', (e) => {
            if (!toggleUsuario.contains(e.target) && !submenu.contains(e.target)) {
                submenu.classList.remove('active');
            }
        });
    }

});