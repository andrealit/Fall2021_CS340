function filterEmployeeByProject() {
    // get pno of selected project from filter dropdown
    var project_pno = document.getElementById('project_filter').value
    // construct the URL and redirect to it
    window.location = '/employee/filter/' + parseInt(project_pno)
}