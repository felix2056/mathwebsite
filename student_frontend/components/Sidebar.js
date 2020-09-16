export default {
   template:`
      <!-- active class remove from router link to replace with .router-link-exact-active -->
      <div class="card">
         <div class="card-body">
            <div class="scroll-element">
               <div class="list-group">
                  <router-link v-for="(question, index) in questions" :key="question.id_Maths_Questions" :to="{ name: 'Questions', params: { exercise: $route.params.exercise, question: question.id_Maths_Questions  } }" class="list-group-item list-group-item-action d-flex justify-content-between">
                     <span class="user-in-table">Question {{ index + 1}}</span>
                  </router-link>
               </div>
            </div>
            <button class="btn btn-success btn-lg btn-block mt-4">Get My Grade</button>
         </div>
      </div>`,

   data: () => ({
      questions: []
   }),

   created() {
      this.getQuestions();
   },

   methods: {
      async getQuestions() {
         var url = `api/getquestions.php?exercise_id=${this.$route.params.exercise}`;
         axios.get(url).then(response => {
            this.questions = response.data
         });
      }
   }
}