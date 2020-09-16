<?php require_once('header.php'); ?>
<?php require_once('sidebar.php'); ?>

<style>
  @import "./components/answer-css.css";
</style>

<section id="app" class="content">
   <div class="content__inner">
      <!--Start all routes-->
         <router-view></router-view>
      <!--/End all routes-->

      <!---EVERYTHING BELOW HERE CAN GO IN footer.php-->
      <?php //require_once('footer.php'); ?>
   </div>
</section>

<!-- Javascript -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<!-- Vue -->
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/vue-router"></script>

<!-- Additional Dependencies -->
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-swal@1.0.0/dist/vue-swal.min.js"></script>

<script type="module">
   Vue.config.devtools = true

   export const bus = new Vue();

   import Quiz from "./components/Quiz.js";
   import Exercise from "./components/Exercise.js";
   import Questions from "./components/Questions.js";

   var routes = [
      {
         path: '/',
         name: 'Quiz',
         component: Quiz
      },
      { 
         path: '/exercise/:exercise',
         name: 'Exercise',
         component: Exercise
      },
      { 
         path: '/exercise/:exercise/questions/:question',
         name: 'Questions',
         component: Questions
      }
   ];

   var router = new VueRouter({
      routes: routes,
      mode: 'hash',
   });

   var app = new Vue({
      el: '#app',
      router: router
   })
</script>