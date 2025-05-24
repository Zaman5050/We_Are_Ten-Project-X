<template>
  <div class="dashboard-table-container">
    <table class="table mb-0 dashboard-table" id="leaves-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Employee Name</th>
          <th>Project Name</th>
          <th>Leave Type</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Total Leaves</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(leave, index) in leaves" :key="index">
          <td>{{ leave.id }}</td>
          <td class="text-decoration-underline"><a class="text-black project-name" :href="'teams/'+leave.user?.uuid">{{ leave.user?.full_name || 'N/A' }}</a></td>
          <td>
            <!-- Ensure projects are rendered correctly -->
            <ul v-if="leave.user?.member_active_projects?.length">
              <li v-for="(project, projectIndex) in leave.user.member_active_projects" :key="projectIndex">
                {{ project.name }}
              </li>
            </ul>
          </td>
          <td>{{ leave.leave_type }}</td>
          <td>{{ leave.start_date }}</td>
          <td>{{ leave.end_date }}</td>
          <td>{{ leave.number_of_days }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

// Props Definition
const props = defineProps({
  leaves: {
    type: Array,
    default: () => [],
  },
});
</script>

<style scoped>
.dashboard-table-container {
  overflow-x: auto;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  text-align: left;
  padding: 8px;
}

.table th {
  background-color: #f4f4f4;
}
.table ul {
    margin-left: 20px;

}
</style>
