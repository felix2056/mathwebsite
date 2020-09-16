import Sidebar from "./Sidebar.js";

export default {
  template: `
      <div class="row">
         <section class="Maths-Questions col-8">
            <div class="card">
               <div :class="'card-body ' + question.Question_Topic">
                  <h1 class="mb-5 q_header">Question - {{ $route.params.question  }}</h1>
                  <h4 class="align-self-center text-center">{{ howToAnswer }}</h4>
                  <!--insert question from db here-->
                  <p class="lead" v-html="question.Question"></p> 

                  <form @submit.prevent="submitAnswer">
                     <div v-if="shouldShow" class="form-group">
                        <label for="answer">Your Answer</label>
                        <div class="input-group mb-2 mr-sm-2">
                           <div class="input-group-prepend">
                              <div class="input-group-text">Rs</div>
                           </div>
                           <input type="number" v-model="answer" class="form-control form-control-lg">
                        </div>
                     </div>
                     <button type="submit" class="btn btn-primary btn-block btn-lg">
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
    question: [],
    answer: "",

    shouldShow: false,
    submitting: false,
  }),

  computed: {
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
        
         default:
            return "Solve the following";
            break;
        }
     }
  },

  mounted() {
    this.getSingle();
    //bus.$emit('changeIt', 'changed header');
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
    async getSingle() {
      var url = `api/getsingle.php?question_id=${this.$route.params.question}`;
      axios.get(url).then((response) => {
        if (
          response.data.Question_Topic == "basic-operations" ||
          response.data.Question_Topic == "writing-numbers" ||
          response.data.Question_Topic == "money-counting" ||
          response.data.Question_Topic == "roman-numbers" ||
          response.data.Question_Topic == "rounding" ||
          response.data.Question_Topic == "HCF" ||
          response.data.Question_Topic == "LCM" || 
          response.data.Question_Topic == "money-conversion" || 
          response.data.Question_Topic == "algebra-word-problems"
        ) {
          this.question = response.data;
          this.answer = response.data.Pupils_Answer;
          this.shouldShow = true;
        } else if (response.data.Question_Topic == "time") {
          this.shouldShow = false;
          this.question = this.populateTQuestions(response.data);
        } else if (response.data.Question_Topic == "missing-numbers") {
          this.shouldShow = false;
          this.question = this.populateMNQuestions(response.data);
        } else if (response.data.Question_Topic == "order-numbers") {
          this.shouldShow = false;
          this.question = this.populateONQuestions(response.data);
        }
      });
    },

    async submitAnswer() {
      var url = "api/submitAnswer.php";
      var question_topic = this.question.Question_Topic;

      if (
        question_topic == "basic-operations" ||
        question_topic == "writing-numbers" ||
        question_topic == "money-counting" ||
        question_topic == "roman-numbers" ||
        question_topic == "rounding" ||
        question_topic == "HCF" ||
        question_topic == "LCM" || 
        question_topic == "money-conversion" || 
        question_topic == "algebra-word-problems"
      ) {
        var ans = this.answer;
        var question_id = this.question.id_Maths_Questions;

        if (ans == "") {
          swal({
            icon: "error",
            title: "Answer Field Is Empty!",
            text:
              "You cannot submit an empty field but you can choose to skip questions then come back and answer them later",
          });

          return;
        }

        this.submitting = true;

        let formData = new FormData();
        formData.append("ans", Number(ans));
        formData.append("question_id", Number(question_id));
        formData.append("question_topic", String(question_topic));

        let headers = { "Content-Type": "multipart/form-data" };

        axios
          .post(url, formData, { headers })
          .then((response) => {
            this.submitting = false;
            swal({
              icon: "success",
            });

            console.log(response);
          })
          .catch((error) => {
            this.submitting = false;
            console.log(error.response);
          });
      } else if (
        question_topic == "missing-numbers" ||
        question_topic == "order-numbers" ||
        question_topic == "time"
      ) {
        var ans, question_id, self;

        self = this;
        ans = [];

        self.submitting = true;

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
        });

        console.log(ans);
        //return;

        question_id = self.question.id_Maths_Questions;

        let formData = new FormData();
        formData.append("ans", ans);
        formData.append("question_id", Number(question_id));
        formData.append("question_topic", String(question_topic));

        let headers = { "Content-Type": "multipart/form-data" };

        axios
          .post(url, formData, { headers })
          .then((response) => {
            this.submitting = false;
            swal({
              icon: "success",
            });

            console.log(response);
            console.log(typeof response.data.ans);
          })
          .catch((error) => {
            this.submitting = false;
            console.log(error.response);
          });
      }
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

      if (typeof result.Pupils_Answer == "string") {
        question = result.Question;
        console.log(question);
      } else {
        question = result.Pupils_Answer;
      }

      html = html + `<li><ul id='q_1'>`;

      for (let j = 0; j < question.length; j++) {
        console.log(question[j]);
        const q = question[j];
        html =
          html +
          ` <li><input type="number" class="form-control" value="${q}" disabled>
         <input type="number" class="form-control q_${this.$route.params.question}" id='q_${this.$route.params.question}'></li>`;
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
  },
};
