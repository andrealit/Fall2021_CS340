function searchEmployeeByFirstName() {
    // get first name
    var first_name_search_string = document.getElementById('first_name_search_string').value
    // construct URL
    window.location = '/employee/search/' + encodeURI(first_name_search_string)
}
