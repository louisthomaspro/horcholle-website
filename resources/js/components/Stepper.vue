<template id="vue-stepper">
<div>
    <div class="flex-container container">
        <div class="step"
            v-for="(step, i) in steps"
            :key="i"
            >
            <div :id="'step-'+step.number" class="step-circle" v-bind:class="{ done: step.number < currentStep, current: step.number === currentStep, failed: fail && (step.number === currentStep) }">
                <span v-if="step.number === currentStep && fail" class="material-icons" aria-hidden="true">error_outline</span>
                <span v-else-if="step.number === currentStep  && !fail" class="material-icons spin-reverse" aria-hidden="true">sync</span>
                <span v-if="step.number > currentStep" class="step-number" aria-hidden="true">{{step.number}}</span>
                <span v-if="step.number < currentStep" class="material-icons" aria-hidden="true">done</span>
            </div>
            <div class="step-label">{{step.label}}</div>
        </div>
    </div>
</div>
</template>
<script>
    export default {
        props: { value: Array },
        data: () => ({ steps: [{ label: 'Synchronisation' }], pBarSize: '', currentStep: 0, fail: false }),
        mounted() {
            console.log("Stepper vue mounted");
            if (!this.value || this.value.length == 0) {
                return;
            }

            this.steps = this.value.map((s, i) => ({ number: i+1, selected: false, ...s}) );

            this.steps[0].selected = true;
        },

        computed: {},

        methods: {
            moveStep(stepNumber) {
                this.currentStep = stepNumber;
            },
            failed() {
                this.fail = true;
            },
            finalize() {
                this.currentStep += 1;
            },
            start() {
                this.fail = false;
                this.moveStep(1);
            }
        }
    }
</script>
