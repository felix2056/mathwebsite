$(document).ready(() => {
    let generated;
    let quiz;

    generate();
});
function generate() {

    var input = {
        'level': $('input[name="level"]:checked').val(),
        'Nquestions': $('#Nquestions').val(),
        'show_f': $('#show_f').is(':checked'),
        'show_c': $('#show_c').is(':checked'),
        'min': $('#min').val(),
        'max': $('#max').val(),
    }
    $.ajax({
        type: 'GET',
        url: "scripts/order-numbers/generate.php",
        data: input,
        success: function (result) {
            if (result == '0') {
                alert('Houston, we have a problem!');
            } else {
                console.log(result);
                result = JSON.parse(result);
                setGenerated(result, input);
                $('#questions').html(populateQuestions(result));
            }

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
            quiz.topic = 'Order Numbers';
            quiz.instruction = result.value[0];
            quiz.total_marks = result.value[1];

            quiz.Nquestions = $('#Nquestions').val();
            quiz.start_date = $('#start_date').val();
            quiz.end_date = $('#end_date').val();
            
            console.log(quiz);

            $.ajax({
                type: 'POST',
                url: "scripts/order-numbers/schedule.php",
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
    for (let i = 0; i < result.length; i++) {

        var question = result[i]['q'];


        html = html + `<li><ul id='q_${i}'>`
        for (let j = 0; j < question.length; j++) {
            const q = question[j];
            const a = result[i]['ans'][j];
            html = html + ` <li><input type="number" class="form-control" value="${q}" disabled><input type="number" class="form-control" ans='${a}' id='q_${i}_${j}'></li>`
        }
        html = html + `
        <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}');">
        Check
     </b-button>
     `;
        html = html + `</li><div id='status_${i}'></div></ul></li>`
    }

    return html;
}

function check(q) {

    inputs = $('#q_' + q + ' :input');
    ans = [];
    cans = [];
    for (let i = 0; i < inputs.length; i++) {
        input = $('#q_' + q + '_' + i.toString()).val();
        cans.push($('#q_' + q + '_' + i.toString()).attr('ans'));
        ans.push(input);
    }

    $.ajax({
        type: 'GET',
        url: "scripts/order-numbers/check.php",
        data: {
            'ans': ans,
            'cans':cans
        },
        success: function (result) {
            if (result == 'true') {
                $('#q_' + q).css('background', 'rgba(0, 255, 0, .1)');
            } else {
                $('#q_' + q).css('background', 'rgba(255, 0, 0, .1)');
                for (let i = 0; i < inputs.length; i++) {
                    a = $('#q_' + q + '_' + i.toString()).attr('ans');
                    $('#q_' + q + '_' + i.toString()).val(a);
                }
            }
        }
    });
}