<template>
  <span class="toggle-like">
      <span class="like" v-on:click="likeCafe( cafe.id )"
            v-if="!liked && cafeLoadStatus === 2 && cafeLikeActionStatus !== 1 && cafeUnlikeActionStatus !== 1">
            喜欢
      </span>
      <span class="un-like" v-on:click="unlikeCafe( cafe.id )"
            v-if="liked && cafeLoadStatus === 2 && cafeLikeActionStatus !== 1 && cafeUnlikeActionStatus !== 1">
            取消喜欢
      </span>

      <loader v-show="cafeLikeActionStatus === 1 || cafeUnlikeActionStatus === 1"
              :width="30"
              :height="30"
              :display="'inline-block'">
      </loader>
  </span>
</template>

<script>
import Loader from "../global/Loader.vue";

export default {
    components: {
        Loader
    },
    computed: {
        cafeLoadStatus() {
            return this.$store.getters.getCafeLoadStatus;
        },

        cafe() {
            return this.$store.getters.getCafe;
        },

        liked() {
            return this.$store.getters.getCafeLikedStatus;
        },

        cafeLikeActionStatus() {
            return this.$store.getters.getCafeLikeActionStatus;
        },

        cafeUnlikeActionStatus() {
            return this.$store.getters.getCafeUnlikeActionStatus;
        }
    },
    methods: {
        likeCafe(cafeID) {
            this.$store.dispatch('likeCafe', {
                id: this.cafe.id
            });
        },
        unlikeCafe(cafeID) {
            this.$store.dispatch('unlikeCafe', {
                id: this.cafe.id
            });
        },
    }
}
</script>

<style lang="scss">
@import '/resources/sass/abstracts/_variables.scss';

span.toggle-like {
    display: block;
    text-align: center;
    margin-top: 30px;

    span.like-toggle {
        display: inline-block;
        font-weight: bold;
        text-decoration: underline;
        font-size: 20px;
        cursor: pointer;

        &.like {
            color: $dark-success;
        }

        &.un-like {
            color: $dark-failure;
        }
    }
}
</style>
