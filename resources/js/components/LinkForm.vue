<template>
    <div class="card card--link">
        <div class="card-body">
            <div class="form-group">
                <label for="url">{{ __('URL') }}</label>
                <input type="text" class="form-control" :class="{'is-invalid': hasFormError('url')}" ref="url" id="url" v-model="form.url" dusk="link-form-url">
                <span class="invalid-feedback" v-if="hasFormError('url')">{{ firstFormError('url') }}</span>
                <small class="form-text text-muted" v-if="parsing" dusk="link-form-parsing-message">{{ __('Retrieving URL informations...') }}</small>
            </div>

            <div class="form-group">
                <label for="title">{{ __('Title') }}</label>
                <input type="text" class="form-control" :class="{'is-invalid': hasFormError('title')}" id="title" v-model="form.title" :disabled="loading" dusk="link-form-title">
                <span class="invalid-feedback" v-if="hasFormError('title')">{{ firstFormError('title') }}</span>
            </div>

            <div class="form-group">
                <label for="content">{{ __('Content') }}</label>
                <textarea id="content" class="form-control" :class="{'is-invalid': hasFormError('content')}" v-model="form.content" :disabled="loading" dusk="link-form-content"></textarea>
                <span class="invalid-feedback" v-if="hasFormError('content')">{{ firstFormError('content') }}</span>
            </div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_private" v-model="form.is_private" :disabled="loading">
                            <label class="custom-control-label" for="is_private" dusk="link-form-private">{{ __('Is private?') }}</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_pinned" v-model="form.is_pinned" :disabled="loading">
                            <label class="custom-control-label" for="is_pinned" dusk="link-form-pinned">{{ __('Is pinned?') }}</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_watched" v-model="form.is_watched" :disabled="loading">
                            <label class="custom-control-label" for="is_watched" dusk="link-is-watched">{{ __("Monitor link's health?") }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('Tags') }}</label>
                <tags v-model="form.tags" dusk="link-form-tags"></tags>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <div>
                <div class="btn-group">
                    <button type="button"
                            class="btn btn-primary"
                            :class="{'btn-danger': hasChanged}"
                            @click.prevent="submit('edit')"
                            :disabled="loading"
                            dusk="link-form-save"
                    >
                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" v-if="loading"></span>
                        {{ __('Save') }}
                    </button>

                    <button type="button"
                            class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                            :class="{'btn-danger': hasChanged}"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            dusk="link-form-save-dropdown"
                    ></button>

                    <div class="dropdown-menu">
                        <button type="button"
                                class="dropdown-item"
                                @click.prevent="submit('view')"
                                dusk="link-form-save-view"
                        >{{ __('Save & View') }}</button>
                        <button type="button"
                                class="dropdown-item"
                                @click.prevent="submit('new')"
                                dusk="link-form-save-new"
                        >{{ __('Save & New') }}</button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary" @click="openArchive" v-if="link">{{ __('Archive')}}</button>
                <a :href="link.permalink" class="btn btn-outline-primary" v-if="link">{{ __('View')}}</a>
            </div>

            <slot name="actions"></slot>
        </div>

        <modal :open="archiving" @closed="archiving = false" size="lg">
            <template #header>{{ __('Archive') }}</template>
            <template #content>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h4>{{ __('Current archive') }}</h4>

                        <p v-if="archive.download">
                            <a :href="link.url_download" class="btn btn-sm btn-primary text-white btn-block">{{ __('Download') }}</a>
                            <confirm class="btn btn-sm btn-danger text-white btn-block" :text="__('Delete')" :text-confirm="__('Confirm')" @confirmed="deleteArchive"></confirm>
                        </p>

                        <div class="alert alert-info" v-else>
                            {{ __('Archive does not exist') }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h4>{{ __('New archive') }}</h4>

                        <p v-if="archive.providers.length > 0">
                            <button class="btn btn-sm btn-primary btn-block" name="type" @click="askArchive('media')" v-if="archive.providers.indexOf('media') !== -1">{{ __('Archive as Media') }}</button>
                            <button class="btn btn-sm btn-primary btn-block" name="type" @click="askArchive('pdf')" v-if="archive.providers.indexOf('pdf') !== -1">{{ __('Archive as PDF') }}</button>
                        </p>

                        <div class="alert alert-info" v-else>
                            {{ __('No provider found to archive this link') }}
                        </div>
                    </div>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
let defaultLink = function () {
    return {
        url: null,
        title: null,
        content: null,
        is_watched: false,
        is_private: false,
        is_pinned: false,
        tags: []
    };
};

import audit from "../mixins/audit";
import formErrors from "../mixins/formErrors";
import httpErrors from "../mixins/httpErrors";

export default {
    mixins: [
        audit,
        formErrors,
        httpErrors,
    ],

    props: {
        queryUrl: {
            type: String,
            required: false
        },
        link: {
            type: Object,
            required: false,
            default: () => {}
        }
    },

    data() {
        return {
            form: defaultLink(),
            parsing: false,
            loading: false,
            archiving: false,
            archive: null,
        }
    },

    mounted() {
        if (this.queryUrl) {
            this.form.url = this.queryUrl;
        }

        if (this.link) {
            this.form = this.link;
        }

        this.startAudit();
    },

    methods: {
        parse() {
            this.loading = true;
            this.parsing = true;

            axios.post('/api/link/parse', {
                url: this.form.url
            }).then(response => {
                this.loading = false;
                this.parsing = false;

                if (response.status === 200) {
                    this.form.title = response.data.title;
                    this.form.content = response.data.content;
                }
            }).catch(error => {
                this.loading = false;
                this.parsing = false;

                this.setFormError(error);
                this.setHttpError(error);
                this.toastHttpError(this.__("Unable to parse link"));
            });
        },

        submit(then) {
            this.loading = true;

            axios.request({
                method: this.link ? 'PUT' : 'POST',
                url: this.link ? this.link.url_update : '/api/link',
                data: this.form
            }).then(response => {
                this.clearAudit();
                this.$toasted.success(this.__('Saved'));

                switch (then) {
                    case 'view':
                        window.location = response.data.post.url;
                        break;
                    case 'new':
                        window.location = '/link/create';
                        break;
                    case 'edit':
                        window.location = `/link/${response.data.post.postable_id}/edit`;
                        break;
                }
            }).catch(error => {
                this.loading = false;
                this.setFormError(error);
                this.setHttpError(error);
                this.toastHttpError(this.__("Can't save"));
            })
        },

        openArchive() {
            axios.get(this.link.url_archive)
                .then(response => {
                    this.archive = response.data;
                    this.archiving = true;
                })
                .catch(error => {
                    this.setHttpError(error);
                    this.toastHttpError(this.__('Whoops!'));
                });
        },

        askArchive(type) {
            axios.put(this.link.url_archive, {type})
                .then(response => {
                    this.$toasted.success(response.data.message);
                    this.archiving = false;
                    this.archive = null;
                })
                .catch(error => {
                    this.setHttpError(error);
                    this.toastHttpError(this.__('Whoops!'));
                });
        },

        deleteArchive() {
            axios.delete(this.link.url_archive)
                .then(response => {
                    this.$toasted.success(response.data.message);
                    this.archiving = false;
                    this.archive = null;
                })
                .catch(error => {
                    this.setHttpError(error);
                    this.toastHttpError(this.__('Whoops!'));
                });
        }
    },

    watch: {
        'form.url': _.debounce(function (value) {
            if (value && ! this.link) {
                this.parse(value)
            }
        }, 500),
    }
}
</script>
