document.addEventListener('DOMContentLoaded', () => {
    const periodBtns = document.querySelectorAll('.chart-period-btn');
    periodBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            periodBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    const chartSvg = document.getElementById('revenueChart');
    const points = document.querySelectorAll('.chart-point');
    const tooltip = document.getElementById('chartTooltip');
    const tooltipDay = document.getElementById('tooltipDay');
    const tooltipVal = document.getElementById('tooltipVal');
    const guideLine = document.getElementById('chartGuideLine');

    if (!chartSvg || !points.length || !tooltip) {
        return;
    }

    points.forEach(point => {
        point.addEventListener('mouseenter', () => {
            const day = point.getAttribute('data-day');
            const val = point.getAttribute('data-val');
            const cx = parseFloat(point.getAttribute('cx'));
            const cy = parseFloat(point.getAttribute('cy'));

            tooltipDay.textContent = day;
            tooltipVal.textContent = val;

            if (guideLine) {
                guideLine.setAttribute('x1', cx);
                guideLine.setAttribute('x2', cx);
                guideLine.style.display = 'block';
            }

            const container = chartSvg.parentElement;
            const rect = container.getBoundingClientRect();
            const scaleX = rect.width / 700;
            const scaleY = rect.height / 180;

            tooltip.style.left = `${cx * scaleX}px`;
            tooltip.style.top = `${cy * scaleY - 45}px`;
            tooltip.style.display = 'flex';
            tooltip.style.opacity = '1';

            point.setAttribute('r', '7');
        });

        point.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
            if (guideLine) {
                guideLine.style.display = 'none';
            }
            const isSun = point.getAttribute('data-day') === 'Sun';
            point.setAttribute('r', isSun ? '5.5' : '4.5');
        });
    });
});
