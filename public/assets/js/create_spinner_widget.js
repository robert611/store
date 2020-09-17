function createSpinnerWidget(type)
{
    let spinner = document.createElement('div');
    spinner.setAttribute('class', `spinner-border text-${type}`);
    spinner.setAttribute('role', 'status')

    let span = document.createElement('span');
    span.setAttribute('class', 'sr-only');
    span.textContent = "Ładowanie...";

    spinner.appendChild(span);

    return spinner;
}