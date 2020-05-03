<aside class="main-sidebar" v-cloak>
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel">
			<div class="pull-left image">
				<img src="{{ load_pic() }}" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info-dash">
				<p>{{ title_case(session('full_name')) }}</p>
				<!-- Status -->
				<a href=""><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>
		
		<!-- search form (Optional) -->
		<form action="#" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
					<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form>
		<!-- /.search form -->
		
		<br/>
		<!-- Sidebar Menu -->
		<ul class="sidebar-menu" data-widget="tree">
			<li class="treeview" id="dealer_treeview">
				<a href="#">
					<i class="fa fa-truck-loading"></i>&nbsp;
					<span>Dealers</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li id="dealer_tab">
						<a href="{{ url('/admin/dealers') }}" style="margin-left: 20px;">
							<span>Dealers</span>
						</a>
					</li>
					<li id="trainor_tab">
						<a href="{{ url('/admin/trainors') }}" style="margin-left: 20px;">
							<span>Trainors</span>
						</a>
					</li>
					<li id="trainee_tab">
						<a href="{{ url('/admin/trainees') }}" style="margin-left: 20px;">
							<span>Trainee</span>
						</a>
					</li>
					<br>
				</ul>
			</li>

			<li class="treeview" id="modules_treeview">
				<a href="#">
					<i class="fa fa-book"></i>&nbsp;
					<span>LEARNING</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li id="module_tab">
						<a href="{{ url('/admin/modules') }}" style="margin-left: 20px;">
							<span>Modules</span>
						</a>
					</li>
					<li id="sub_module_tab">
						<a href="{{ url('/admin/submodules') }}" style="margin-left: 20px;">
							<span>Module Exams</span>
						</a>
					</li>
					<br>
				</ul>
			</li>

			<li class="treeview" id="exams_treeview">
				<a href="#">
					<i class="fa fa-file-alt"></i>&nbsp;
					<span>SCHEDULES</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li id="module_schedule_tab">
						<a href="{{ url('/admin/module_schedules') }}" style="margin-left: 20px;">
							<span>Module Schedule</span>
						</a>
					</li>

					<li id="exam_schedule_tab">
						<a href="{{ url('/admin/exam_schedules') }}" style="margin-left: 20px;">
							<span>Exam Schedule</span>
						</a>
					</li>
					<br>
				</ul>
			</li>

			<li class="treeview" id="scores_treeview">
				<a href="#">
					<i class="fa fa-vials"></i>&nbsp;
					<span>SCORE & RESULTS</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li id="exam_result_tab">
						<a href="{{ url('/admin/results/exam_schedules') }}" style="margin-left: 20px;">
							<span>Exam Results</span>
						</a>
					</li>
					<br>
				</ul>
			</li>

			<li class="treeview" id="controls_treeview">
				<a href="#">
					<i class="fa fa-users"></i>&nbsp;
					<span>SESSIONS</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li id="active_user_tab">
						<a href="{{ url('/admin/controls/active_users') }}" style="margin-left: 20px;">
							<span>Active Users</span>
						</a>
					</li>
					<br>
				</ul>
			</li>
		</ul>
	</section>
</aside>

@push('scripts')
<script>new Vue({el: '.main-sidebar'})</script>
<script>
	$('.menu-sidebar').slimScroll({
		disableFadeOut: true,
		wheelStep: 10
	});
</script>
@endpush