<template>
    <div id="vue-preloader">
        <div class="vue-loader"></div>
    </div>
</template>
<style lang="scss" scoped>
#vue-preloader {
    position: absolute; /* Position it relative to the parent */
    top: 0;
    left: 0;
    height: 100%; /* Take full height of the parent */
    width: 100%;  /* Take full width of the parent */
    background: rgba(255,255,255,.79); /* Background for loader */
    display: flex;    /* Flexbox for centering the loader */
    justify-content: center;
    align-items: center;
    z-index: 10; /* Ensure it appears above content */
}

.vue-loader {
    height: 50px;
    width: 50px;
    border-radius: 50%;
    position: relative;

    &:before, &:after {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        background: #000;
        border-radius: 50%;
        opacity: 0;
        animation: popin 1.5s linear infinite;
    }

    &:after {
        animation-delay: 0.5s; /* Staggered animation */
    }
}

@keyframes popin {
    0% {
        transform: scale(0);
        opacity: 0.5;
    }
    50% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(0);
        opacity: 0;
    }
}

</style>
