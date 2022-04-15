'use strict';

const mainEndpoint = todosSettings.mainEndpoint;
const currentUser = todosSettings.currentUser || false;

// visibility filters
const filters = {
	all: tasks => tasks,
	active: tasks => tasks.filter(task => !task.completed),
	completed: tasks => tasks.filter(task => task.completed),
	search: (tasks, word) => tasks.filter(task => task.title.includes(word))
};

// main list component
const taskListComponent = {
	data: () => ({
		tasks: [],
		newTask: "",
		editedTask: null,
		visibility: "all",
		showSearch: false,
		searchValue: "",
		error: "",
		pluralizeLang: ["task", 'tasks'],
		users: [],
		currentUser: currentUser,
	}),
	watch: {
		tasks: {
			immediate: true,
			handler: function (newValue, oldValue) {

				if (newValue == oldValue && this.editedTask === null) {
					this.saveList()
				}

			},
			deep: true,
			flush: 'post'
		}
	},
	computed: {
		filteredTasks: function () {

			return filters[this.visibility](this.tasks, this.searchValue);
		},

		remaining: function () {

			let n = filters.active(this.tasks).length;
			return n
		},

		allDone: {

			get: function () {
				return this.remaining === 0;
			},

			set: function (value) {
				this.tasks.forEach(function (task) {
					task.completed = value;
				});
			}
		}
	},

	methods: {
		//could be any ajax but jquery is in wordpress as default
		jqueryAjax(method, endpoint, successFunction, data = undefined) {
			jQuery.ajax({
				type: method,
				url: endpoint,
				data: data ? JSON.stringify(data) : null,
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', todosSettings.nonce);
				},
				success:
					function (data) {
						if (data.response && data.response.code && data.response.code == 200) {
							successFunction(data.body)
						}
					}
			});
		},

		checkCurrentUser() {
			return this.currentUser && (this.currentUser !== null || this.currentUser != undefined)
		},

		getTasksById(event) {
			this.currentUser = event.target.value;

			if (!this.currentUser) return;

			let extraParam = this.checkCurrentUser() ? `?id=${this.currentUser}` : ''
			this.jqueryAjax("GET", mainEndpoint + `/tasks` + extraParam, this.assignList);
		},

		getList(endpoint, callback) {
			this.jqueryAjax("GET", mainEndpoint + "/" + endpoint, callback);
		},

		saveList() {
			let extraParam = this.checkCurrentUser() ? `?id=${this.currentUser}` : ''
			this.jqueryAjax("POST", mainEndpoint + "/tasks" + extraParam, this.assignList, this.tasks)
		},

		assignUsers(list) {
			this.users = list
		},

		assignList(list) {
			this.tasks = list
		},

		addNewTask: function () {
			if (this.newTask.trim() == '') {
				let th = this;
				this.error = "Task is empty";
				setTimeout(() => { th.error = "" }, 3000);
				return
			}
			this.tasks.push({
				title: this.newTask && this.newTask.trim(),
				completed: false
			});

			this.newTask = ''
		},

		removeTask: function (task) {
			this.tasks.splice(this.tasks.indexOf(task), 1);
		},

		editTask: function (task) {
			this.beforeEditCache = task.title;
			this.editedTask = task;
		},

		toggleComplete(task, event) {
			task.completed = event.checked;
		},

		doneEdit: function (task) {
			if (!this.editedTask) {
				return;
			}

			this.editedTask = null;
			task.title = task.title.trim();

			if (!task.title) {
				this.removeTask(task);
			}

			this.saveList();
		},

		cancelEdit: function (task) {
			this.editedTask = null;
			task.title = this.beforeEditCache;
		},

		removeCompleted: function () {
			this.tasks = filters.active(this.tasks);
			this.saveList();
		},

		toggleSearch() {

			this.showSearch = !this.showSearch;

			if (this.showSearch == false) {
				this.visibility = "all";
				this.searchValue = "";
			}
		},

		pluralize(count) {
			return this.pluralizeLang[!(count === 1) * 1]
		},

		taskLength() {
			return newTask.length > 0
		}
	},
	directives: {
		"task-focus": function (el, binding) {
			if (binding.value) {
				el.focus();
			}
		}
	},
	mounted() {
		if (currentUser) {
			this.getList('users', this.assignUsers);
		}
		this.getList('tasks', this.assignList);
	},
}

// create vue3
const app = Vue.createApp(taskListComponent);

// mount
app.mount("#todoapp");
