$(document).ready(() => {
    let generated;
    let quiz;

    generate();
});
function generate() {
    var input = {
        'operation': $('input[name="operation"]:checked').val(),
        'Nquestions': $('#Nquestions').val(),
        'f1': $('#f1').val(),
        'f2': $('#f2').val()
    }
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

function setGenerated(result) {
    generated = result;
}


function schedule() {
    Swal.mixin({
        title: 'Schedule This Exercise Set?',
        text: "Commence scheduling and save this excercise to database!",
        icon: 'info',
        showLoaderOnConfirm: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Next &rarr;',
        progressSteps: ['1', '2', '3']
      }).queue([
        {
            title: 'Quiz Instructions!',
            text: 'Insert basic instructions for this quiz',
            input: 'textarea'
        },
        {
            title: 'Quiz Total Marks!',
            text: 'Insert total average score for this quiz (0-100)',
            input: 'number'
        },
        {
            title: 'Start & End Dates!',
            html: '<p>Insert date of the commencement of this quiz</p><input id="start_date" type="date" class="swal2-input"><p>Insert date of the conclusion of this quiz</p><input id="end_date" type="date" class="swal2-input">'
        }
      ]).then((result) => {
        if (result.value) {
            quiz = {};
            quiz.topic = 'Basic Operation';
            quiz.instruction = result.value[0];
            quiz.total_marks = result.value[1];

            quiz.Nquestions = $('#Nquestions').val();
            quiz.start_date = $('#start_date').val();
            quiz.end_date = $('#end_date').val();
            
            console.log(quiz);

            $.ajax({
                type: 'POST',
                url: "scripts/basic-operations/schedule.php",
                dataType: "json",
                data: { generatedData: generated, quizData: quiz },
                success: function (result) {
                    if (result.code == "200"){
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaction Successful!',
                            text: String(result.msg)
                        })

                        //result = JSON.parse(result);
                        console.log(result);
                        // $('#questions').html(populateQuestions(result));
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Transaction Error',
                            text: String(result.msg),
                            footer: '<a href>Why do I have this issue?</a>'
                        })
            
                        // $(".display-error").html("<ul>"+data.msg+"</ul>");
                        // $(".display-error").css("display","block");
                    }
                }
            });
        }
    })
}

function populateQuestions(result) {

    var html = ''

    for (let j = 0; j < result.length; j++) {
        const question = result[j];
        var q = question["q"].toString();
        var ans = question['ans'];
        html = html + `<li><div class="d-flex flex-row"><div>${q}`;
        html = html + `<p><input type="number" class="form-control" id="q_${j}" ans="${ans}"></p>`
        html = html + `<b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check(${j})">
        Check
    </b-button></div></div>`

        html = html + '</li>'
    }
    return html;
}

function check(q) {
    ans = $('#q_' + q.toString()).val();
    cans = $('#q_' + q.toString()).attr('ans');


    $.ajax({
        type: 'GET',
        url: "scripts/basic-operations/check.php",
        data: {
            'ans': ans,
            'cans': cans
        },
        success: function (result) {

            if (result=='true') {
                $('#q_' + q).css('background', 'rgba(0, 255, 0, .1)');
            } else {
                $('#q_' + q).css('background', 'rgba(255, 0, 0, .1)');
                a = $('#q_' + q.toString()).attr('ans');
                $('#q_' + q.toString()).val(a);
            }
        }
    });
}