<div>
    <div class="stm-lms-multimedia" v-if="media.length">
        <div class="stm-lms-multimedia__single" v-for="(m, k) in media">
            <div class="row center">
                <div class="column column-60">
                    <input type="text" v-model="media[k]['url']"
                           placeholder="<?php esc_html_e('Media URL', 'masterstudy-lms-learning-management-system') ?>"/>
                    <div class="stm-lms-multimedia__image" v-if="m.type === 'image' && m.preview.length > 0">
                        <img v-bind:src="m.preview" />
                    </div>
                </div>
                <div class="column column-40">
                    <div class="stm-lms-multimedia__actions">
                        <div class="lnr lnr-file-add" @click="addFile(k)"></div>
                        <i class="lnr lnr-trash"
                           @click="removeMedia(k, '<?php esc_html_e('Do you really want to delete this media?', 'masterstudy-lms-learning-management-system') ?>')"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="stm-lms-button-group">
        <a class="button" @click="addMedia($event)"><?php esc_html_e('Add media', 'masterstudy-lms-learning-management-system'); ?></a>
        <a class="button button-outline"
           @click="addMediaBulk($event)"><?php esc_html_e('Bulk upload', 'masterstudy-lms-learning-management-system'); ?></a>
    </div>

</div>