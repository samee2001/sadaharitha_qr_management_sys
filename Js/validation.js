// Client-side validation
        const form = document.getElementById('qrForm');
        form.addEventListener('submit', function(event) {
            let valid = true;
            const rangeStart = document.getElementById('rangeStart');
            const rangeStep = document.getElementById('rangeStep');
            const year = document.getElementById('year');
            const bot = document.getElementById('bot');
            const cellColorSelect = document.getElementById('cellColorSelect');

            if (!rangeStart.value || parseInt(rangeStart.value) < 1) {
                rangeStart.classList.add('is-invalid');
                valid = false;
            } else {
                rangeStart.classList.remove('is-invalid');
            }

            if (!rangeStep.value || parseInt(rangeStep.value) < 1 || parseInt(rangeStep.value) > 5000) {
                rangeStep.classList.add('is-invalid');
                valid = false;
            } else {
                rangeStep.classList.remove('is-invalid');
            }

            if (!year.value.trim()) {
                year.classList.add('is-invalid');
                valid = false;
            } else {
                year.classList.remove('is-invalid');
            }

            if (!bot.value.trim()) {
                bot.classList.add('is-invalid');
                valid = false;
            } else {
                bot.classList.remove('is-invalid');
            }

            if (!cellColorSelect.value) {
                cellColorSelect.classList.add('is-invalid');
                valid = false;
            } else {
                cellColorSelect.classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        // Color preview
        document.getElementById('cellColorSelect').addEventListener('change', function() {
            const color = this.options[this.selectedIndex].getAttribute('data-color') || '255,255,255';
            document.getElementById('colorPreview').style.backgroundColor = `rgb(${color})`;
        });