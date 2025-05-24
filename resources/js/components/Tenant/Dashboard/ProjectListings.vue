
<template>
<div class="dashboard-table-container">
    <table class="table mb-0 dashboard-table" >
        <thead>
            <tr>
                <th>ID</th>
                <th>Project Name</th>
                <th>Project Type</th>
                <th>Address</th>
                <th>Currency </th>
                <th>Stage</th>
                <th>Completion</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(project, index) in projects" :key="index">
                <td>{{ project.id }}</td>
                <td class="text-decoration-underline"><a class="text-black project-name" :href="'projects/'+project.uuid">{{ project.name }}</a></td>
                <td>{{ project.type }}</td>
                <td>{{ project.address }}</td>
                <td> <span class="mx-3">{{ project?.currency?.symbol}}</span></td>
                <td> <span class="stage text-uppercase text-decoration-underline">{{ project?.stages[0]?.stage_name ?? '---' }}</span></td>
                <td>
                    <div class="container" style="display: flex; align-items: center; gap:10px">
                        <div class="circle x-small" :data-fill="+project?.project_completion_percentage" style="--color: #252C32;">
                            <div class="bar"></div>
                        </div>
                        <span style="position: relative;">
                            <span>{{ project?.project_completion_percentage }}%</span>
                        </span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

</template>
<script setup>
import { defineProps } from "vue";
 

const props = defineProps({
    projects: {
        type: Array,
        default: [],
    },
});
</script>
