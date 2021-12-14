<template>
    <div>
        <input type="hidden" :name="`${namespacedName}[formatted]`" v-model="$root.$data.data.address['formatted']" />
        <input type="hidden" :name="`${namespacedName}[raw]`" v-model="$root.$data.data.address['raw']" />
    </div>
</template>

<script>
    export default {
        data() {
            return {
                namespacedName: this.$root.$data.namespacedName
            }
        },
        watch: {
            '$parent.lat': function () {
                this.validateMeta();
            },
            '$parent.lng': function () {
                this.validateMeta();
            }
        },
        methods: {
            validateMeta: function () {
                // If coordinates are invalid
                if (!this.$parent.validCoords()) {
                    // Reset meta fields
                    this.$root.$data.data.address['formatted'] = null;
                    this.$root.$data.data.address['raw'] = null;
                }
            }
        }
    }
</script>
