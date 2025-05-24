<script setup>
import { computed, ref, defineProps , defineEmits, watch} from "vue";
import Avatar from "@/components/Avatar.vue";

defineEmits(['avatarClick']);

// Define component props
const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  size: {
    type: Number,
    default: 0,
  },
  uindex: {
    type: Number,
    default: Math.random(),
  },
  maxLength: {
    type: Number,
    default: 3
  },
  you: {
    type: Boolean,
    default: false
  }
});

// Constants and reactive state
const extraMembers = ref([]);
const loopIndexRef = ref(false);

// Computed property to calculate the number of extra members
const plusMembersCount = computed(() => {
  return Math.max(props.size - props.maxLength, 0);
});

// Utility function to generate a random color
const randomColor = () => {
  const getRandomValue = () => Math.floor(Math.random() * 151);
  return `#${getRandomValue().toString(16).padStart(2, "0")}${getRandomValue()
    .toString(16)
    .padStart(2, "0")}${getRandomValue().toString(16).padStart(2, "0")}`;
};

// Handle extra members when there are more than props.maxLength
const handleExtraMembers = computed(() => {
  if (props.size > props.maxLength) {
    loopIndexRef.value = props.users.length > props.maxLength;
    extraMembers.value = props.users.slice(props.maxLength);
  }
});


const getShortName = (user) => {
  return currentAuth.authUuid == user.uuid && props.you ? "you" : user.short_name 
};

// Initialize computed property to execute its logic
handleExtraMembers.value;


watch( 
    () => handleExtraMembers.value,
    (newVal) => {
      loopIndexRef.value = props.users.length > props.maxLength;
      extraMembers.value = props.users.slice(props.maxLength);
    },
    {deep: true}
 )

</script>

<template>
  <div v-if="props.size" class="d-flex">
    <!-- Loop through users and display Avatar or handle extra members -->
    <template v-for="(user, uIndex) in props.users" :key="user.uuid">
      <span :title="user.full_name" @click.stop="$emit('avatarClick', user.uuid)">
        <Avatar 
        v-if="uIndex < props.maxLength"
        :name="getShortName(user)"
        :background="randomColor()"
        :active="user?.active"
        :path="user.profile_picture"
      />
      </span>
    </template>

    <!-- Dropdown for extra members -->
    <div
      v-if="loopIndexRef && plusMembersCount > 0"
      class="dropdown member-img-dropdown member-img-icon d-inline-block"
    >
      <button
        class="dropdown-toggle bg-transparent border-0"
        type="button"
        :id="'dropdownMenuButton' + uindex"
        data-bs-toggle="dropdown"
        aria-expanded="false"
      >
        <div
          class="member-img-icon bg-white d-flex align-items-center justify-content-center remaining-member-list"
        >
          +{{ plusMembersCount }}
        </div>
      </button>
      <ul class="dropdown-menu" :aria-labelledby="'dropdownMenuButton' + uindex">
        <!-- List extra members in dropdown -->
        <template v-for="(user, index) in extraMembers" :key="index">
          <li v-if="user" class="dropdown-item d-flex align-items-center gap-2 px-1" @click.stop="$emit('avatarClick', user.uuid)">
            <Avatar :name="user.short_name" :background="randomColor()" :active="user?.active" />
            <p class="text-truncate mw-100">{{ user.full_name }}</p>
          </li>
        </template>
      </ul>
    </div>
  </div>
  <!-- Fallback if no users are provided -->
  <div v-else>
  </div>
</template>

<style>
.dropdown.member-img-dropdown .dropdown-toggle::after {
  display: none;
}
.remaining-member-list {
  border: 1px solid #d5dadd;
  font-size: 9px;
}
</style>
