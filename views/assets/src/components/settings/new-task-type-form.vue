<template>
    <form class="type-form" action="" method="post" @submit.prevent="selfNewType()">
        <div class="form-group-wrap">
            <div class="form-group">
                <div class="type-wrap">
                    <h3>{{ __('Type Name', 'wedevs-project-manager') }}</h3>
                </div>
                <div>
                    <input v-model="formData.title" class="form-control" :placeholder="__( 'Type name...', 'wedevs-project-manager' )" type="text">
                </div>
            </div>

            <div class="form-group">
                <div class="type-wrap">
                    <h3>{{ __('Description', 'wedevs-project-manager') }}</h3>
                </div>
                <div>
                    <input v-model="formData.description" class="form-control description-control" :placeholder="__( 'Description (optional)', 'wedevs-project-manager' )" type="text">
                </div>
            </div>
        </div>

        <div>

            <div class="type-action" v-if="formData.id">
                <a  @click.prevent="closeEditForm()" class="pm-button pm-secondary" href="#">{{ __('Cancel', 'wedevs-project-manager') }}</a>
                <div class="update-button-wrap">
                    <input :class="getUpdateButtonClass()" type="submit" :value="__('Update', 'wedevs-project-manager')">
                    <div v-if="updating" class="pm-spinner-circle"></div>
                </div>

            </div>

            <div class="type-action" v-else>
                <a @click.prevent="closeNewTypeForm()" v-if="formVisibility.isClickNewForm" class="pm-button pm-secondary" href="#">{{ __('Cancel', 'wedevs-project-manager') }}</a>
                <div class="add-button-wrap">
                    <input v-if="!formData.id" :class="getNewTypeButtonClass()" type="submit" :value="__('Create type', 'wedevs-project-manager')">
                    <div v-if="adding" class="pm-spinner-circle"></div>
                </div>
            </div>
        </div>
    </form>
</template>

<style lang="less">
    .type-form {
        .type-action {
            display: flex;
            align-items: center;
            .pm-secondary {
                margin-right: 5px !important;
            }
            .update-button-wrap, .add-button-wrap {
                position: relative;

                .update-text-color, .add-text-color {
                    color: #1A9ED4 !important;
                }

                .pm-spinner-circle {
                    &:after {
                        content: "";
                        border: 2px solid #fff;
                        border-radius: 50%;
                        height: 1em;
                        width: 1em;
                        position: absolute;
                        left: 50%;
                        margin-left: -8.5px;
                        top: 50%;
                        margin-top: -8.5px;
                        border-color: #fff #fff #fff transparent;
                        -webkit-animation: pm-spinner-circle 1s infinite;
                        animation: pm-spinner-circle 1s infinite;
                    }
                }
            }
        }
    }


    @keyframes pm-spinner-circle {
        0% {
            -webkit-transform: rotate(0);
            transform: rotate(0);
            animation-timing-function: cubic-bezier(.55,.055,.675,.19)
        }

        50% {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
            animation-timing-function: cubic-bezier(.215,.61,.355,1)
        }

        to {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg)
        }
    }

    @-webkit-keyframes pm-spinner-circle {
        0% {
            -webkit-transform: rotate(0);
            transform: rotate(0);
            animation-timing-function: cubic-bezier(.55,.055,.675,.19)
        }

        50% {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
            animation-timing-function: cubic-bezier(.215,.61,.355,1)
        }

        to {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg)
        }
    }


</style>

<script type="text/javascript">
    export default {
        props: {
            formVisibility: {
                type: [Object],
                default () {
                    return {}
                }
            },

            taskType: {
                type: [Object],
                default () {
                    return {}
                }
            }
        },

        data () {
            return {
                preventDoubleClick: false,
                updating: false,
                adding: false,
                formData: {
                    id: '',
                    title: '',
                    description: '',
                    status: 1,
                    type: 'task'
                }
            }
        },
        created () {
            if ( parseInt( this.taskType.id ) ) {
                this.formData = { ...this.taskType }
            }
        },
        methods: {
            getNewTypeButtonClass () {
                if(this.adding) {
                    return 'pm-button pm-primary add-text-color';
                }

                return 'pm-button pm-primary';
            },

            getUpdateButtonClass () {
                if(this.updating) {
                    return 'pm-button pm-primary update-text-color';
                }

                return 'pm-button pm-primary';
            },

            closeNewTypeForm () {
                this.formVisibility.isClickNewForm = false;
            },

            closeEditForm (data) {
                this.$emit('udpateFormClose');
            },

            checkValidation (type) {
                if(type.title === '') {
                    pm.Toastr.error(__('Type name required!', 'wedevs-project-manager'));
                    return false;
                }

                return true;
            },

            selfNewType () {
                var self = this;
                if(this.preventDoubleClick) {
                    return;
                }

                if( ! this.checkValidation( self.formData ) ) {
                    return false;
                }

                this.preventDoubleClick = true;

                var args = {
                    data: {
                        id: self.formData.id,
                        title: self.formData.title,
                        description: self.formData.description,
                        type: self.formData.type,
                        status: 1,
                    },
                    callback(res) {
                        
                    }
                }

                if( parseInt(self.taskType.id) ) {
                    self.updating = true;
                    this.updateType(args);
                } else {
                    self.adding = true;
                    this.newType(args);
                }
            },


            updateType (args) {
                var self = this;

                var request = {
                    data: args.data,
                    type: 'POST',
                    url: self.base_url + 'pm/v2/settings/task-types/'+args.data.id,
                    success (res) {
                        self.$store.commit( 'settings/updateTaskType', res.data );
                        self.closeEditForm();
                        self.preventDoubleClick = false;
                        self.updating = false;
                    }
                };

                self.httpRequest(request);
            },

            newType (args) {
                var self = this;
    
                var request = {
                    data: args.data,
                    type: 'POST',
                    url: self.base_url + 'pm/v2/settings/task-types',
                    success (res) {
                        self.$store.commit( 'settings/setTaskType', res.data );

                        self.formData.id = '';
                        self.formData.title = '';
                        self.formData.description = '';
                        self.formData.type = 'task';

                        self.adding = false;
                        self.preventDoubleClick = false;
                    }
                };

                self.httpRequest(request);
            }
        }
    }

</script>
