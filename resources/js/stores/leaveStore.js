// resources/js/stores/authStore.js
import { defineStore } from "pinia";

export const useLeavStore = defineStore("leave-store", {
    state: () => ({
        leave: {},
        leaveStatus: '',
        member: '',
    }),
    actions: {
        setLeave(leave){
            this.leave = Object.assign({}, leave);
        },
        setLeaveStatus(leaveStatus){
            this.leaveStatus = leaveStatus;
        },
        setMember(member){
            this.member = member;
        },
    },
    getters: {},
});
