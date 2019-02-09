<template>
    <span>
    <span v-if="!editing">
      <span v-text="value" @click="enableEditing" style="white-space: pre-wrap;"></span>
    </span>
    <div v-if="editing" >

        <div style="font-family: Arial;font-size: 16px;">
            <div class="form-group mb-1">
            <textarea :disabled="saving" v-model="tempValue" class="form-control" aria-label="With textarea"></textarea>
          </div>
             <div class="text-right mt-1">
            <button @click="saveEdit" class="btn btn-primary" type="button">Save<span v-if="saving">&nbsp;<i class="fa fa-spinner fa-spin"></i></span></button>
            <button @click="disableEditing" class="btn btn-light ml-2" type="button">Cancel</button>
            </div>
            
        </div>

    </div>
</span>
</template>
<script>
    export default {

        data: () => ({
            value: null,
            tempValue: null,
            editing: false,
            page: null,
            context: null,
            id : null,
            saving: false
          }),

        mounted() {
            this.value = this.$el.getAttribute('data-value');
            this.page = this.$el.getAttribute('data-page');
            this.context = this.$el.getAttribute('data-context');
            this.id = this.$el.getAttribute('data-id');
        },


        methods: {
            enableEditing: function(){
            this.tempValue = this.value;
            this.editing = true;
            },
            disableEditing: function(){
            this.tempValue = null;
            this.editing = false;
            },
            saveEdit: function(){

                this.saving = true;

                axios
                  .put('api/text/update', {
                      value: this.tempValue,
                      page: this.page,
                      context: this.context,
                        id : this.id
                    })
                  .then(response => {
                    this.value = this.tempValue;
                  })
                  .catch(error => {
                    console.log(error);
                    this.tempValue = this.value;
                  })
                  .finally(() => {
                    this.saving = false;
                    this.disableEditing();
                  });

                
            }
        }
    }
</script>