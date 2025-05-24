<template>

    <div class="modal fade " id="add-participant" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <h2 class="create-project-title mb-4">Add Participant</h2>

                    <multiselect class="nav-input project-selector" id="To" v-model="selectedParticipant"
                        :close-on-select="false" :options="props.members"
                        placeholder="Select to" label="full_name" track-by="uuid" :multiple="true" :taggable="false"
                        open-direction="bottom" :show-labels="false">
                        <!-- <template #selection="{ values, search, isOpen }">
                            <span class="multiselect__single" v-if="values.length" v-show="!isOpen">{{ values.length }}
                                members selected</span>
                        </template> -->
                    </multiselect>

                    <ul v-if="selectedParticipant" class="list-group mt-3">
                        <li class="list-group-item" v-for="(member, key) in selectedParticipant" :key="key">
                            {{ member.full_name }}
                        </li>
                    </ul>

                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button style="width: 148px;" type="button" class="apply-leave-btn" id="addParticipents" @click="HandleParticipant">Add</button>
                </div>
            </div>
        </div>
    </div>

</template>


<script setup>
import {
    defineEmits,
    defineProps,
    computed,
    watch,
    ref,
    onMounted
} from "vue";
import Multiselect from "vue-multiselect";

const emit = defineEmits(['updateParticipents']);
const props = defineProps({
    members: {
        type: Array,
        default: () => []
    },
    chat: {
        type: Object,
        default: () => { }
    },
    selectedMembers: {
        type: Array,
        default: () => []
    }
});

const HandleParticipant = () => { 

    emit('updateParticipents', {
        chatUuid: props.chat?.uuid,
        participents: selectedParticipant.value,
    });

    $('#add-participant') && $('#add-participant').modal('hide');
};

const selectedParticipant = ref(props.selectedMembers ?? [])

watch(
    () => props.selectedMembers,
    (newVal) => {
        selectedParticipant.value = newVal;
    },
    { deep: true }
)

</script>