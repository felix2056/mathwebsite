export default {
  template: `
      <!-- active class remove from router link to replace with .router-link-exact-active -->
      <div class="card">
         <div class="card-body">
            <div class="scroll-element">
               <div class="list-group">
                  <router-link v-for="(question, index) in questions" :key="question.id_Maths_Questions" :id="'sidebarQ_' + question.id_Maths_Questions" :class="question.Question_Status" :to="{ name: 'Questions', params: { exercise: $route.
                     params.exercise, question: question.id_Maths_Questions  } }" class="list-group-item list-group-item-action d-flex justify-content-between">
                     <span class="user-in-table">Question {{ index + 1}}</span>
                     <span :class="question.Question_Grade" class="icon"></span>
                  </router-link>
               </div>
            </div>
            <button v-show="!isGraded" :disabled="!hasAnsweredAll" @click="getMyGrade()" class="btn btn-success btn-lg btn-block mt-4">Get My Grade</button>
         </div>
      </div>`,

  data() {
    return {
      questions: [],
      hasAnsweredAll: false,

      isGraded: false
    };
  },

  watch: {
    "$route.params.question": {
      handler: function () {
        this.getQuestions();
        this.checkHasAnsweredAll();
      },
      deep: true,
      immediate: true,
    },
  },

  mounted() {
    this.getQuestions();
  },

  methods: {
    checkPropAvailable() {
      if (!this.questions) {
        this.getQuestions();
      }
    },

    async getQuestions() {
      var url = `api/getquestions.php?exercise_id=${this.$route.params.exercise}`;
      axios.get(url).then((response) => {
        this.questions = response.data;

        //check at least one answer is already graded then hide get grades button
        if (this.questions[0].Graded == "1") {
          this.isGraded = true;
        }
      });
    },

    async checkHasAnsweredAll() {
      var url = `api/getHasAnsweredAll.php?exercise_id=${this.$route.params.exercise}`;
      
      axios.post(url).then((response) => {
        this.hasAnsweredAll = response.data;
      });
    },

    async getMyGrade() {
      if (!this.hasAnsweredAll) {
         swal("You haven't answered all the questions", {
            icon: "error",
          });

         return;
      }

      swal({
         title: "Grade Your Answers?",
         text: "Proceed to submit if you have answered all the questions!",
         icon: "warning",
         buttons: true,
         dangerMode: true,
       })
       .then((willSubmit) => {
         if (willSubmit) {
            var url = `api/getMyGrade.php?exercise_id=${this.$route.params.exercise}`;
      
            axios.get(url).then((response) => {
               this.$router.go(this.$router.currentRoute)
            });
         } 
       });
    }
  }
};
