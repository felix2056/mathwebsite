$(document).ready(() => {
    fetchQuestions();
});
function fetchQuestions() {
    $.ajax({
        type: 'GET',
        url: "scripts/basic-operations/generate.php",
        data: input,
        success: function (result) {
            result = JSON.parse(result);

            setGenerated(result, input);
            $('#questions').html(populateQuestions(result));
        }
    });
}