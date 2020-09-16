$(document).ready(() => {

    generate();
});
function generate() {
    var std = $('input[name="sdt"]:checked').val();
    var input = {
        'level': $('input[name="level"]:checked').val(),
        'Nquestions': $('#Nquestions').val(),
    }
    $.ajax({
        type: 'GET',
        url: "scripts/shopping-problems/generate.php",
        data: input,
        success: function (result) {
            if (result == '0') {
                alert('Houston we have a problem!');
            } else {
                console.log(result);
                result = JSON.parse(result);
                $('#questions').html(populateQuestions(result));
            }

        }
    });
}

function populateQuestions(result, mixed = false) {
    var html = ''
    for (let i = 0; i < result.length; i++) {

        var question = result[i]['q'];
        var answer = result[i]['a'];


        html = html + `<li>`;
        html = html + question;

        html = html.replace("<q1>", `<input type="number" class="form-control" id="q_${i}1" ans='${answer[0]}'' >
            <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}1');">
            Check
         </b-button>`)
        html = html.replace("<q2>", `<input type="text" class="form-control" id="q_${i}2" ans='${answer[1]}' '>
            <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}2');">
            Check
         </b-button>`);
        html = html.replace("<q3>", `<input type="text" class="form-control" id="q_${i}3" ans='${answer[2]}''>
            <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}3');">
            Check
         </b-button>`);
         html = html.replace("<q4>", `<input type="text" class="form-control" id="q_${i}4" ans='${answer[3]}''>
            <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}4');">
            Check
         </b-button>`);
         html = html.replace("<q5>", `<input type="text" class="form-control" id="q_${i}5" ans='${answer[4]}''>
            <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}5');">
            Check
         </b-button>
         </li>`);
    }

    return html;
}

function check(q) {

    inputs = $('#q_' + q + ' :input');
    ans = $('#q_' + q.toString()).val();
    cans = $('#q_' + q.toString()).attr('ans');


    $.ajax({
        type: 'GET',
        url: "scripts/shopping-problems/check.php",
        data: {
            'ans': ans,
            'cans': cans
        },
        success: function (result) {
            if (result == 'true') {
                $('#q_' + q).css('background', 'rgba(0, 255, 0, .1)');
            } else {
                $('#q_' + q).css('background', 'rgba(255, 0, 0, .1)');
                a = $('#q_' + q.toString()).attr('ans');
                equ = $('#q_' + q.toString()).attr('equ');
                $('#q_' + q.toString()).val(a);
                $('#status_' + q.toString()).html(equ);

            }
        }
    });
}