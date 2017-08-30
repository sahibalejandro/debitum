<template>
</template>

<script>
export default {
    props: ['dataPeriod', 'dataDesignator'],

    data() {
        return {
            repeat: this.dataPeriod !== null,
            period: this.dataPeriod || 1,
            designator: this.dataDesignator || 'weeks',
        }
    }
}
</script>
