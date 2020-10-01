import Sidebar from "./Sidebar.js";

export default {
  template: `
      <div class="row">
         <section class="Maths-Questions col-8">
         <div v-if="question.Question_Grade != ''" id="grade" class="card mb-4">
              <div class="card-body">
                <h3>{{ message.header }}</h3>
                <p class="lead">{{ message.text }}</p>
                <h3><span :class="message.badge" class="badge">{{ message.percent }}</span></h3>
              </div>
            </div>
            <div class="card">
               <div :class="'card-body ' + question.Question_Topic">
                  <!-- <h1 class="mb-5 q_header">Question - {{ $route.params.question  }}</h1> -->
                  <h4 class="align-self-center text-center">{{ howToAnswer }}</h4>
                  <!--insert question from db here-->
                  <p v-html="question.Question"></p> 
                  <div @click="getSolution()" v-show="isGraded" v-if="shouldShowSolution && isGraded" class="solution mb-2">
                    <details>
                      <summary>Solution</summary>
                      <p v-html="solution"></p>
                    </details>
                  </div>
                  
                  <form @submit.prevent="parseSubmitAnswer">
                    <label v-if="question.Question_Grade == 'tick'" for="answer" class="bg-success text-white">Your Answer is correct</label>
                    <label v-else-if="question.Question_Grade == 'close'" for="answer" class="bg-danger text-white">Your Answer is incorrect</label>
                    <label v-else for="answer">Your Answer</label>
                    
                    <div v-if="shouldShow" class="form-group">
                        <div class="input-group mb-2 mr-sm-2">
                           <input type="number" step="0.01" :disabled="isGraded" v-model="answer" class="form-control form-control-lg">
                        </div>
                     </div>
                     <div v-if="isGraded" class="correctAns">
                        <p>Correct answer is <strong>{{ question.Answer }}</strong></p>
                      </div>
                     <button v-if="!isGraded" type="submit" class="btn btn-primary btn-block btn-lg">
                      {{ submitting ? '' : 'SUBMIT' }}
                      <i v-show="submitting" class="fa fa-spinner fa-spin"></i>
                     </button>
                  </form>
                  <!--end question from DB-->
               </div>
            </div>
         </section>
         
         <section id="MathsQuestionsList" class="col-4">
            <sidebar></sidebar>
         </section>
      </div>`,

  components: { Sidebar },

  data: () => ({
    questions: [],
    question: [],
    questionToUse: "",

    answer: "",
    solution: "",

    shouldShow: false,
    submitting: false,

    message: {
      header: "",
      text: "",
      percent: "",
      badge: "",
    },
  }),

  computed: {
    correctAnswers() {
      return this.questions.filter((item) => {
        return item.Question_Grade == "tick";
      });
    },

    howToAnswer() {
      switch (this.question.Question_Topic) {
        case "basic-operations":
          return "Complete the following sums";
          break;
        case "missing-numbers":
          return "Fill in the missing numbers";
          break;

        case "order-numbers":
          return "Order numbers";
          break;
        case "writing-numbers":
          return "Write the number";
          break;
        case "rounding":
          return "Round to the underlined digit";
          break;
        case "HCF":
          return "Find the Highest Common Factor";
          break;
        case "LCM":
          return "Find the Lowest Common Multiple";
          break;
        case "long-division":
          return "Divide the following";
          break;

        case "place-value-as-words":
          return "Type the number";
          break;

        case "place-value":
          return "Determine the place value of the underlined digit";
          break;

        default:
          return "Solve the following";
          break;
      }
    },

    shouldShowSolution() {
      return this.question.Solution != null;
    },

    isGraded() {
      switch (this.question.Graded) {
        case "1":
          return true;
          break;

        case "0":
          return false;
          break;

        case null:
          return false;
          break;

        default:
          return false;
          break;
      }
    },
  },

  mounted() {
    this.getQuestions().then(() => {
      this.getSingle();
    });
  },

  watch: {
    "$route.params.question": {
      handler: function () {
        this.getSingle();
      },
      deep: true,
      immediate: true,
    },
  },

  methods: {
    //To populate the sidebar and navigate to next question when student answers
    async getQuestions() {
      var url = `api/getquestions.php?exercise_id=${this.$route.params.exercise}`;
      axios.get(url).then((response) => {
        this.questions = response.data;
      });
    },

    async getSingle() {
      var url = `api/getsingle.php?question_id=${this.$route.params.question}`;
      axios.get(url).then((response) => {
        if (
          response.data.Question_Topic == "basic-operations" ||
          response.data.Question_Topic == "writing-numbers" ||
          response.data.Question_Topic == "money-counting" ||
          response.data.Question_Topic == "roman-numbers" ||
          response.data.Question_Topic == "HCF" ||
          response.data.Question_Topic == "LCM" ||
          response.data.Question_Topic == "money-conversion" ||
          response.data.Question_Topic == "algebra-word-problems" ||
          response.data.Question_Topic == "original-price"
        ) {
          this.question = response.data;
          this.answer = response.data.Pupils_Answer;
          this.shouldShow = true;
        } else if (response.data.Question_Topic == "rounding") {
          this.shouldShow = false;
          this.question = this.populateRQuestions(response.data);
        } else if (response.data.Question_Topic == "time") {
          this.shouldShow = false;
          this.question = this.populateTQuestions(response.data);
        } else if (response.data.Question_Topic == "time-conversion") {
          this.shouldShow = false;
          this.question = this.populateTCQuestions(response.data);
        } else if (response.data.Question_Topic == "missing-numbers") {
          this.shouldShow = false;
          this.question = this.populateMNQuestions(response.data);
        } else if (response.data.Question_Topic == "order-numbers") {
          this.shouldShow = false;
          this.question = this.populateONQuestions(response.data);
        } else if (response.data.Question_Topic == "long-division") {
          this.shouldShow = false;
          this.questionToUse = response.data.Question;
          this.question = this.populateLDQuestions(response.data);
        } else if (response.data.Question_Topic == "place-value-as-words") {
          this.shouldShow = false;
          this.question = this.populatePVAWQuestions(response.data);
        } else if (response.data.Question_Topic == "shopping-problems") {
          this.shouldShow = false;
          this.question = this.populateSPQuestions(response.data);
        } else if (response.data.Question_Topic == "place-value") {
          this.shouldShow = false;
          this.question = this.populatePVQuestions(response.data);
        } else if (response.data.Question_Topic == "rearrange-formula") {
          this.shouldShow = false;
          this.question = this.populateRFQuestions(response.data);
        } else if (response.data.Question_Topic == "speed-time-distance") {
          this.question = this.populateSTDQuestions(
            response.data,
            response.data.Solution == "mixed"
          );
        }

        if (this.question.Question_Grade != "") {
          //Question has been graded
          this.setGrades();
        }
      });
    },

    async getSolution() {
      let question_topic = this.question.Question_Topic;

      if (question_topic == "long-division") {
        this.parseLDSolution();
      } else if (question_topic == "time") {
        this.parseTSolution();
      } else if (question_topic == "original-price") {
        this.parseOPSolution();
      }
    },

    async submitAnswer() {
      let url = "api/submitAnswer.php";

      let question_id = this.question.id_Maths_Questions;
      let question_topic = this.question.Question_Topic;
      let ans = this.answer;

      //let sidebarLink = $('#sidebarQ_' + this.question.id_Maths_Questions);

      if (this.submitting) {
        let formData = new FormData();
        formData.append("ans", JSON.stringify(ans));
        formData.append("question_id", Number(question_id));
        formData.append("question_topic", String(question_topic));

        let headers = { "Content-Type": "multipart/form-data" };

        axios
          .post(url, formData, { headers })
          .then((response) => {
            this.submitting = false;

            //Resolve the question sidebar link as answered
            // if (!sidebarLink.hasClass('answered')) {
            //   sidebarLink.addClass('answered')
            // }

            this.goToNext();

            console.log(response);
          })
          .catch((error) => {
            this.submitting = false;
            console.log(error);
          });
      }
    },

    populateRQuestions(result) {
      var html = "";

      const question = result.Question;

      var q = question["q"].toString();
      var place = question["place"];

      console.log(place);

      html = html + "<li><span>";
      for (let i = 0; i < q.length; i++) {
        var number = q[i];
        if (i == place - 1) {
          html = html + `<u>${number}</u>`;
        } else {
          html = html + `${number}`;
        }
      }
      html = html + `</span><span> = </span>`;

      if (result.Pupils_Answer == "") {
        html =
          html +
          `<input type="number" min="0" class="form-control q_${this.$route.params.question}" id='q_${this.$route.params.question}'>`;
      } else {
        html =
          html +
          `<input type="number" min="0" class="form-control q_${
            this.$route.params.question
          }" id='q_${this.$route.params.question}' value="${Number(
            result.Pupils_Answer
          )}">`;
      }

      html = html + "</li>";

      result.Question = html;
      return result;
    },

    populateMNQuestions(result) {
      var html, question;

      html = "";

      html = html + '<ul id="questions" class="d-flex flex-wrap">';

      // console.log(typeof(result.Pupils_Answer));
      // return;

      if (typeof result.Pupils_Answer == "string") {
        question = result.Question;
        console.log(question);
      } else {
        question = result.Pupils_Answer;
      }

      html = html + `<li><ul> <li class="d-flex flex-wrap" id='q_1'>`;
      for (let j = 0; j < question.length; j++) {
        const q = question[j];

        if (q.toString() == "") {
          html =
            html +
            `<input type="number" class="form-control q_${this.$route.params.question}" id='q_${this.$route.params.question}' />`;
        } else {
          html =
            html +
            `<input type="number" class="form-control q_${this.$route.params.question}" value = '${q}' id='q_${this.$route.params.question}' disabled/>`;
        }
      }

      html = html + `</li><div id='status_1'></div></ul></li>`;

      html = html + "</ul>";

      result.Question = html;
      return result;
    },

    populateONQuestions(result) {
      console.log(result.Question);
      var html, question;

      html = "";

      html = html + '<ul id="questions" class="d-flex flex-wrap">';

      // console.log(typeof(result.Pupils_Answer));
      // return;

      question = result.Question;

      html = html + `<li><ul id='q_1'>`;

      if (typeof result.Pupils_Answer == "string") {
        for (let j = 0; j < question.length; j++) {
          console.log(question[j]);
          const q = question[j];
          html =
            html +
            ` <li><input type="number" class="form-control" value="${q}" disabled>
           <input type="number" class="form-control q_${this.$route.params.question}" id='q_${this.$route.params.question}'></li>`;
        }
        console.log(question);
      } else {
        for (let j = 0; j < question.length; j++) {
          console.log(question[j]);
          const q = question[j];
          html =
            html +
            ` <li><input type="number" class="form-control" value="${q}" disabled>
           <input type="number" min="0" class="form-control q_${this.$route.params.question}" id='q_${this.$route.params.question}' value="${result.Pupils_Answer[j]}"></li>`;
        }
      }

      html = html + `</li><div id='status_1'></div></ul></li>`;

      result.Question = html;
      return result;
    },

    populateTQuestions(result, mixed = false) {
      var html, question;

      html = "";

      question = result.Question;

      html = html + `<li>`;
      question = question.split("choice").join(`mC1`);
      question = question.split("opt").join(`opt_1_`);
      console.log(question);
      html = html + question;

      if (typeof result.Pupils_Answer == "string") {
        html = html.replace(
          "<q1>",
          `<input type="text" class="q1 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" >`
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="q2 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" >`
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="q3 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}">`
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="q4 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}">`
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="q5 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}">
               </li>`
        );
      } else {
        html = html.replace(
          "<q1>",
          `<input type="text" class="q1 form-control q_${this.$route.params.question}" value="${result.Pupils_Answer[0]}" id="q_${this.$route.params.question}" >`
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="q2 form-control q_${this.$route.params.question}" value="${result.Pupils_Answer[1]}" id="q_${this.$route.params.question}" >`
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="q3 form-control q_${this.$route.params.question}" value="${result.Pupils_Answer[2]}" id="q_${this.$route.params.question}">`
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="q4 form-control q_${this.$route.params.question}" value="${result.Pupils_Answer[3]}" id="q_${this.$route.params.question}">`
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="q5 form-control q_${this.$route.params.question}" value="${result.Pupils_Answer[4]}" id="q_${this.$route.params.question}">
               </li>`
        );
      }

      html = html.replace("<sol>", `sol_1`);

      result.Question = html;
      return result;
    },

    populateTCQuestions(result, mixed = false) {
      var html, question, answer;

      html = "";

      question = result.Question;
      answer = result.Answer;

      html = html + `<li>`;
      console.log(question);
      html = html + question;

      var qq = "";

      this.questionToUse = answer.split(" ")[1];

      if (result.Pupils_Answer == "") {
        qq +=
          `<div class="input-group ml-2">
              <input type="text" class="form-control q_${this.$route.params.question}"  id="q_${this.$route.params.question}">
            <div class="input-group-append"><div class="input-group-text">` +
          answer.split(" ")[1] +
          `</div>
            </div></div>`;
      } else {
        qq +=
          `<div class="input-group ml-2">
              <input type="text" class="form-control q_${
                this.$route.params.question
              }"  id="q_${this.$route.params.question}" value="${Number(
            result.Pupils_Answer.split(" ")[0]
          )}"">
            <div class="input-group-append"><div class="input-group-text">` +
          answer.split(" ")[1] +
          `</div>
            </div></div>`;
      }

      qq += `</div>`;

      html = html.replace("<q1>", qq);
      var i = 1;

      html = html.replace(
        "<op-ch>",
        ` <input type="text" class="form-control ml-2"  id="q_${i}" ans='${answer[0]}'>
          <div class="form-group ml-2">
             <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="ampm_${i}" value="AM" id="am_${i}">
                <label class="custom-control-label" for="am_${i}">AM</label>
             </div>
             <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="ampm_${i}" value="PM" id="pm_${i}">
                <label class="custom-control-label" for="pm_${i}">PM</label>
             </div>
          </div>
          </div>
          `
      );

      html = html.replace("<ID>", `ans_${i}`);

      result.Question = html;
      return result;
    },

    populateLDQuestions(result) {
      var html, question;

      html = "";

      question = result.Question;

      html = "";

      if (typeof result.Pupils_Answer == "string") {
        html =
          html +
          `<li>
              <p class="text-left"> <span id='d_1'>${question["d"]}</span>
                <span id='D_1'>${question["D"]}</span>
              </p>
              <p class="mb-0">
                <span class="form-group d-flex justify-content-between">
                    <label>Answer:</label>
                    <input type="number" min="0" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}">
                    <label>R:</label>
                    <input type="number" min="0" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}">
                </span>
              </p>
              <div id='result_1'></div>
          </li>`;
      } else {
        html =
          html +
          `<li>
              <p class="text-left"> <span id='d_1'>${question["d"]}</span>
                <span id='D_1'>${question["D"]}</span>
              </p>
              <p class="mb-0">
                <span class="form-group d-flex justify-content-between">
                    <label>Answer:</label>
                    <input type="number" min="0" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[0]}">
                    <label>R:</label>
                    <input type="number" min="0" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[1]}">
                </span>
              </p>
              <div id='result_1'></div>
          </li>`;
      }

      result.Question = html;
      return result;
    },

    populatePVAWQuestions(result) {
      var html = "";

      var question = result.Question;
      var answer = result.Answer;

      if (typeof result.Pupils_Answer == "string") {
        html = html + `<li>`;
        question = question
          .split("choice")
          .join(`mC${this.$route.params.question}`);
        question = question
          .split("opt")
          .join(`opt_${this.$route.params.question}_`);
        console.log(question);
        html = html + question;
        html = html.replace(
          "<q1>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}"' >`
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}"'>`
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}"' >`
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}"' >`
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}"' >
         </li>`
        );
      } else {
        html = html + `<li>`;
        question = question
          .split("choice")
          .join(`mC${this.$route.params.question}`);
        question = question
          .split("opt")
          .join(`opt_${this.$route.params.question}_`);
        console.log(question);
        html = html + question;
        html = html.replace(
          "<q1>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[0]}"' >`
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[1]}"'>`
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[2]}"' >`
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[3]}"' >`
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[4]}"' >
         </li>`
        );
      }

      html = html.replace("ID1", `ans_${this.$route.params.question}11`);
      html = html.replace("ID2", `ans_${this.$route.params.question}12`);

      result.Question = html;
      return result;
    },

    populatePVQuestions(result) {
      var html = '<ul class="d-flex flex-wrap">';

      const question = result.Question;
      let answer = result.Answer;

      var q = question["q"].toString();
      var place = question["place"];
      var ans = JSON.parse(answer);

      html = html + "<li><span>";
      for (let i = 0; i < q.length; i++) {
        var number = q[i];
        if (i == place - 1) {
          html = html + `<u>${number}</u>`;
        } else {
          html = html + `${number}`;
        }
      }
      html = html + `</span><span> = </span>`;

      for (let i = ans.length - 1; i >= 0; i--) {
        html =
          html +
          `<div class="custom-control custom-radio custom-control-inline">
            <input type="radio" class="custom-control-input q_${
              this.$route.params.question
            }" id="q_${this.$route.params.question}_${i}" name="placevalue-q_${
            this.$route.params.question
          }" value='${i}' place='${ans.length - place}'>
            <label class="custom-control-label" for="q_${
              this.$route.params.question
            }_${i}">${ans[i]}</label>
          </div>`;
      }

      html = html + `<div id="ans_1"></div>`;

      html = html + "</li>";

      html = html + "</ul>";

      result.Question = html;
      return result;
    },

    populateRFQuestions(result) {
      var html = "";

      var question = result.Question;
      var answer = result.Answer;

      html = html + `<li>`;
      html = html + question;

      if (typeof result.Pupils_Answer == "string") {
        html = html.replace(
          "<q1>",
          `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}1" ans='${answer[0]}' >
            `
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}2" ans='${answer[0]}' >
             `
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}3" ans='${answer[1]}''>
           `
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}4" ans='${answer[2]}''>
            `
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}5" ans='${answer[3]}''>
         </li>`
        );
      } else {
        html = html.replace(
          "<q1>",
          `<input type="number" class="q1 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}1" value="${result.Pupils_Answer[0]}"' >
              `
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class=" q2 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}2" value="${result.Pupils_Answer[1]}" >
               `
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="q3 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}3" value="${result.Pupils_Answer[2]}"'>
             `
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="q4 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}4" value="${result.Pupils_Answer[3]}"'>
              `
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="q5 form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}5" value="${result.Pupils_Answer[4]}"'>
           </li>`
        );
      }

      result.Question = html;
      return result;
    },

    populateSPQuestions(result, mixed = false) {
      var html = "";

      var question = result.Question;
      var answer = result.Answer;

      html = html + `<li>`;
      html = html + question;

      if (typeof result.Pupils_Answer == "string") {
        html = html.replace(
          "<q1>",
          `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}1"'>`
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}2"'>`
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}3"'>`
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}4"'>`
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}5"'>
          </li>`
        );
      } else {
        html = html.replace(
          "<q1>",
          `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}1" value="${result.Pupils_Answer[0]}"'>`
        );
        html = html.replace(
          "<q2>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}2" value="${result.Pupils_Answer[1]}"'>`
        );
        html = html.replace(
          "<q3>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}3" value="${result.Pupils_Answer[2]}"'>`
        );
        html = html.replace(
          "<q4>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}4" value="${result.Pupils_Answer[3]}"'>`
        );
        html = html.replace(
          "<q5>",
          `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}5" value="${result.Pupils_Answer[4]}"'>
          </li>`
        );
      }

      result.Question = html;
      return result;
    },

    populateSTDQuestions(result, mixed = false) {
      var html = "";

      var question = result.Question;
      var answer = result.Answer;

      if (typeof result.Pupils_Answer == "string") {
        html = html + `<li>`;
        html = html + question;
        if (mixed == true) {
          html = html.replace(
            "<q1>",
            `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}1" >`
          );
          html = html.replace(
            "<q2>",
            `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}2" >`
          );
          html = html.replace(
            "<q3>",
            `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}3">
         </li></ul></li>`
          );
        } else {
          html =
            html +
            `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}">`;
        }
      } else {
        html = html + `<li>`;
        html = html + question;
        if (mixed == true) {
          html = html.replace(
            "<q1>",
            `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}1" value="${result.Pupils_Answer[0]}" >`
          );
          html = html.replace(
            "<q2>",
            `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}2" value="${result.Pupils_Answer[1]}" >`
          );
          html = html.replace(
            "<q3>",
            `<input type="text" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}3" value="${result.Pupils_Answer[2]}">
         </li></ul></li>`
          );
        } else {
          html =
            html +
            `<input type="number" class="form-control q_${this.$route.params.question}" id="q_${this.$route.params.question}" value="${result.Pupils_Answer[0]}">`;
        }
      }

      result.Question = html;
      return result;
    },

    parseSubmitAnswer() {
      let ans, question_id, question_topic, self;

      question_id = this.question.id_Maths_Questions;
      question_topic = this.question.Question_Topic;

      self = this;
      self.submitting = true;

      if (
        question_topic == "basic-operations" ||
        question_topic == "writing-numbers" ||
        question_topic == "money-counting" ||
        question_topic == "roman-numbers" ||
        question_topic == "HCF" ||
        question_topic == "LCM" ||
        question_topic == "money-conversion" ||
        question_topic == "algebra-word-problems"
      ) {
        if (this.answer == "") {
          swal({
            icon: "error",
            title: "Answer Field Is Empty!",
            text:
              "You cannot submit an empty field but you can choose to skip questions then come back and answer them later",
          });

          self.submitting = false;
          return;
        }

        //console.log(ans);
        //return Number(ans);
      } else if (
        question_topic == "rounding" ||
        question_topic == "time-conversion"
      ) {
        let ans = $(".q_" + self.$route.params.question).val();

        if (ans == "") {
          swal({
            icon: "error",
            title: "No Answer Provided!",
            text:
              "You cannot submit an empty field but you can choose to skip questions then come back and answer them later",
          });

          this.submitting = false;
          return;
        }

        console.log(ans);

        if (question_topic == "time-conversion") {
          //Append word time to answer (hours, mins, secs)
          this.answer = ans + " " + this.questionToUse;
        } else {
          this.answer = ans;
        }
      } else if (
        question_topic == "missing-numbers" ||
        question_topic == "order-numbers" ||
        question_topic == "rounding" ||
        question_topic == "long-division" ||
        question_topic == "place-value-as-words" ||
        question_topic == "time" ||
        question_topic == "rearrange-formula" ||
        question_topic == "shopping-problems" || 
        question_topic == "speed-time-distance"
      ) {
        ans = [];

        $(".q_" + self.$route.params.question).each(function (index) {
          var input = $(this).val();
          index = index + 1;

          if (input == "") {
            swal({
              icon: "error",
              title: "Field " + index + " Is Empty!",
              text:
                "You cannot submit an empty field but you can choose to skip questions then come back and answer them later",
            });

            self.submitting = false;
            return;
          }

          ans.push(input);
          console.log(ans);

          self.answer = ans;
        });
      } else if (question_topic == "place-value") {
        let radio = $(
          `input[name="placevalue-q_${this.$route.params.question}"]`
        );

        if (!radio.is(":checked")) {
          swal({
            icon: "error",
            title: "Unchecked Answer!",
            text:
              "You cannot submit an empty field but you can choose to skip questions then come back and answer them later",
          });

          self.submitting = false;
          return;
        }

        var checked = $(
          `input[name="placevalue-q_${this.$route.params.question}"]:checked`
        );
        var answer = checked.val();
        var Canswer = checked.attr("place");

        console.log("answer-" + answer + " Canswer-" + Canswer);

        if (answer == Canswer) {
          //Set pupils answer to the real answer of this question
          self.answer = JSON.parse(self.question.Answer);
        } else {
          //Not correct so override answer to wrong and so we don't commit actual answer to DB
          self.answer = [0];
        }
      }

      this.submitAnswer();
    },

    parseLDSolution() {
      let question = this.questionToUse;
      let solution = this.question.Solution;

      let html = "<table class='table table-bordered table-responsive'><tr>";

      //result = JSON.parse(solution);
      let result = solution;
      var ans = [" "].concat(result["ans"]);
      ans.forEach((i) => {
        console.log("haha:" + (i == " "));

        if (i == " ") {
          html = html + "<td>" + i + "</td>";
        } else {
          html = html + "<td class='border-bottom-thick-black'>" + i + "</td>";
        }
      });

      html =
        html +
        "</tr><tr><td class='border-right-thick'>" +
        question["d"] +
        "</td>";
      for (let i = 0; i < question["D"].length; i++) {
        const n = question["D"][i];
        html = html + "<td>" + n + "</td>";
      }

      html = html + "</tr><tr>";
      var R = result["result"];

      for (let i = 0; i < R.length - 1; i++) {
        if (i % 2 == 0) {
          html = html + "<td>&minus;</td>";
        } else {
          html = html + "<td></td>";
        }

        for (let j = 0; j < R[i].length; j++) {
          let n = R[i][j];
          if (n == " ") {
            if (j != 0 && i % 2 == 0 && R[i][j - 1] != " ") {
              html = html + "<td> <span class='drop-arrow'>&darr;</span> </td>";
            } else {
              html = html + "<td>" + n + "</td>";
            }
          } else {
            if (i % 2 == 0) {
              html =
                html + "<td class='border-bottom-thick-gray'>" + n + "</td>";
            } else {
              html = html + "<td>" + n + "</td>";
            }
          }
        }
        html = html + "</tr><tr>";
      }
      html = html + "<td></td>";
      for (let j = 1; j < R[R.length - 1].length; j++) {
        const n = R[R.length - 1][j];
        html = html + "<td>" + n + "</td>";
      }
      html += "</tr></table>";

      console.log(html);
      this.solution = html;
    },

    parseTSolution() {
      let solution = this.question.Solution;

      this.solution = solution;
    },

    parseOPSolution() {
      let solution = this.question.Solution;

      this.solution = solution;
    },

    goToNext() {
      let questions = [];

      for (let index = 0; index < this.questions.length; index++) {
        questions.push(this.questions[index]["id_Maths_Questions"]);
      }

      console.log(questions);

      const currentQuestion = questions.indexOf(this.$route.params.question);
      const nextQuestion = (currentQuestion + 1) % questions.length;

      this.$router.push({
        name: "Questions",
        params: {
          exercise: this.$route.params.exercise,
          question: questions[nextQuestion],
        },
      });
    },

    setGrades() {
      var percent = (this.correctAnswers.length / this.questions.length) * 100;

      if (percent > 10 && percent < 30) {
        this.message.header = "Average: Try harder next time";
        this.message.badge = "badge-danger";
      } else if (percent > 30 && percent < 50) {
        this.message.header = "Good: You can do better";
        this.message.badge = "badge-warning";
      } else if (percent > 50 && percent < 80) {
        this.message.header = "Great: Well done";
        this.message.badge = "badge-warning";
      } else if (percent > 80) {
        this.message.header = "Perfect: Keep it up";
        this.message.badge = "badge-success";
      } else {
        this.message.header = "Poor: You need improvements";
        this.message.badge = "badge-danger";
      }

      this.message.percent = percent + "%";
      this.message.text =
        "You got " +
        this.correctAnswers.length +
        " out of " +
        this.questions.length +
        " questions correct";
    },
  },
};
