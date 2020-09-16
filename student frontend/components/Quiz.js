export default {
    template:`
    <section class="Maths-Quiz col-12 mb-4">
        <div v-if="exercises.length">
            <div v-for="(exercise, index) in exercises" :key="index" class="card mb-5">
                <div class="card-body">
                    <div class="scroll-element">
                        <div class="list-group">
                            <h2>Quiz {{ exercise.id_Maths_Excercise_Sets + ' - ' + exercise.Maths_Topic }}</h2>
                        </div>
                        <p>{{ exercise.Maths_Topic_Instruction }}</p>
                    </div>

                    <div>
                        <h4>Quiz Details</h4>
                        <ul>
                            <li>Number of questions: {{ exercise.Num_Questions }}</li>
                            <li>Total marks available: {{ exercise.Total_Marks_Available }}</li>
                            <li>Date of commencement: {{ formatDate(exercise.Start_Date) }}</li>
                            <li>Date of conclusion: {{ formatDate(exercise.End_Date) }}</li>
                            <li>Status: {{ exercise.Status }}</li>
                        </ul>
                    </div>
                    <router-link :to="{ name: 'Exercise', params: { exercise: exercise.id_Maths_Excercise_Sets }}" class="btn btn-success btn-lg btn-block mt-4">
                        Start The Quiz
                    </router-link>
                </div>
            </div>
        </div>

        <div v-else class="card mb-10">
            <div class="card-body">
                <div class="scroll-element">
                    <div class="list-group">
                        <h2>Nothing To Display!</h2>
                    </div>
                    <p>There are currently no quizes available at this time. Check back some other time</p>
                </div>
            </div>
        </div>
    </section>`,

    data: () => ({
        exercises: []
    }),

    mounted() {
        var url = 'api/getexercises.php'
        axios.get(url).then(response => {
            if (response.data != null) {
                this.exercises = response.data   
            }
        });
    },

    methods: {
        formatDate(date) {
          return moment(date).format("Do MMMM YYYY, hh:mm A")
        }
      }
  }