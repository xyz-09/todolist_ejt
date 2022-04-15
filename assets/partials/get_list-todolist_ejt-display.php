
<noscript>
    <style>
        #todoapp{
            display:none
        }
    </style>
    <p class="error">JS must be Enabled</p>
</noscript>
<section id="todoapp" class="todoTable">
    <span class="choose_user" v-show="users.length">

        <label>
            <?php _e('Choose user', 'todolist_ejt'); ?>
        </label>

        <select @change="getTasksById">
            <optgroup name="choose" label="<?php _e('Choose user', 'todolist_ejt'); ?>">
                <option @change="getTasksById" v-for="user in users" :selected="user.ID==currentUser" :value="user.ID">
                    {{user.display_name}}
                </option>
            </optgroup>
        </select>

    </span>

    <span class="error" style="display:none" v-show="error.length != 0">{{error}}</span>

    <header class="todo_header">
        <input class="new-todoTask" autocomplete="off" placeholder="<?php _e('Type your todo task', 'todolist_ejt'); ?>" v-model="newTask" @keyup.enter="addNewTask">
        <button class="new-todoTask-button" @click="addNewTask" :show="taskLength"></button>
        <span :class="`search_icon ${showSearch==true?'filtered':''}`" @click="toggleSearch">
            <svg fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30px" height="30px">
                <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z" id="path2" />
                <path id="path823" d="m 8.0320247,9.4582668 3.7200003,3.6500002 v 5.69 c 0,0.37 0.39,0.61 0.72,0.45 l 2,-1 c 0.17,-0.09 0.28,-0.26 0.28,-0.45 v -4.69 l 3.719999,-3.6500002 a 0.5,0.5 0 0 0 -0.35,-0.85 H 8.3920247 a 0.5,0.5 0 0 0 -0.36,0.85 z" inkscape:connector-curvature="0" style="fill:none;stroke:currentColor" />
            </svg>
        </span>

        <span class="searchBox" v-show="showSearch">
            <input type="search" @keyup.enter="visibility='search'" @blur="visibility='search'" v-model="searchValue" name="search" />
        </span>

    </header>

    <section class="main" v-show="tasks.length" v-cloak="">

        <div class="completed-wrapper">
            <input id="toggle-all" class="toggle-all" type="checkbox" v-model="allDone">

            <label for="toggle-all">
                <?php _e('Complete all tasks', 'todolist_ejt'); ?>
            </label>

            <button class="clear-completed" @click="removeCompleted">
                <?php _e('Clear completed', 'todolist_ejt'); ?>
            </button>
        </div>

        <transition-group class="todo-list" name="todo" tag="ul">

            <li v-for="(task,index) in filteredTasks" class="task" :key="index" :class="{ completed: task.completed, editing: task == editedTask }">
                <div class="view">
                    <input class="toggle" type="checkbox" v-model="task.completed" @change="toggleComplete(task, $event.target)">

                    <label @dblclick="editTask(task)">
                        {{ task.title }}
                    </label>

                    <label class="label"></label>
                    <button class="destroy" @click="removeTask(task)"></button>
                </div>

                <input class="edit" type="text" v-model="task.title" v-task-focus="task == editedTask" @blur="doneEdit(task)" @keyup.enter="doneEdit(task)" @keyup.esc="cancelEdit(task)" />
            </li>
        </transition-group>
    </section>

    <footer class="footer" v-show="tasks.length" v-cloak>
        <span class="task-count">
            <strong @click="filter('active')">
                {{ remaining }} {{ pluralize(remaining)}}
            </strong>
            <?php _e('left', 'todolist_ejt'); ?>
        </span>

        <ul class="filters">
            <li>
                <a @click="visibility='all'" :class="{ selected: visibility == 'all' }">
                    <?php _e('All', 'todolist_ejt'); ?>
                </a>
            </li>
            <li>
                <a @click="visibility='active'" :class="{ selected: visibility == 'active' }">
                    <?php _e('Uncomplete', 'todolist_ejt'); ?>
                </a>
            </li>
            <li>
                <a @click="visibility='completed'" :class="{ selected: visibility == 'completed' }">
                    <?php _e('Completed', 'todolist_ejt'); ?>
                </a>
            </li>
        </ul>
    </footer>
</section>