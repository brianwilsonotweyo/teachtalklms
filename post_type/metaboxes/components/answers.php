<div>
    <div class="stm-lms-questions">

        <!--Simple Question-->
        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-single_choice"
                 v-if="choice == 'single_choice' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_radio" v-bind:class="{'active' : v.isTrue}">
                        <input type="radio" v-bind:name="choice + '_' + origin" v-model="correctAnswer"
                               v-bind:value="v.text" @change="isAnswer()"/>
                        <i></i>
                        <input type="text" v-model="questions[k]['text']"/>

                        <textarea v-model="questions[k]['explain']"
                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'masterstudy-lms-learning-management-system') ?>"></textarea>
                    </label>
                    <div class="actions">
                        <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                    </div>
                </div>

            </div>
        </transition>

        <!--Multi Answer Question-->
        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-multi_choice"
                 v-if="choice == 'multi_choice' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_checkbox" v-bind:class="{'active' : v.isTrue}">
                        <input type="checkbox" v-bind:name="choice" v-model="correctAnswers[v.text]"
                               v-bind:value="v.text" @change="isAnswers()"/>
                        <i class="fa fa-check"></i>
                        <input type="text" v-model="questions[k]['text']"/>

                        <textarea v-model="questions[k]['explain']"
                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'masterstudy-lms-learning-management-system') ?>"></textarea>

                    </label>
                    <div class="actions">
                        <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                    </div>
                </div>

            </div>
        </transition>

        <!--True False Question-->
        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-true_false"
                 v-if="choice == 'true_false' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_radio" v-bind:class="{'active' : v.isTrue}">
                        <input type="radio" v-bind:name="choice" v-model="correctAnswer" v-bind:value="v.text"
                               @change="isAnswer()"/>
                        <i></i>
                        <span>{{ v.text }}</span>
                    </label>
                </div>

            </div>
        </transition>

        <!--Item Match Question-->
        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-item_match"
                 v-if="choice == 'item_match' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_checkbox" v-bind:class="{'active' : v.isTrue}">
                        <div class="row">
                            <div class="column">
                                <h6><?php esc_html_e('Question', 'masterstudy-lms-learning-management-system'); ?></h6>
                                <input type="text" v-model="questions[k]['question']"/>
                            </div>
                            <div class="column">
                                <h6><?php esc_html_e('Match', 'masterstudy-lms-learning-management-system'); ?></h6>
                                <input type="text" v-model="questions[k]['text']"/>
                            </div>
                        </div>

                        <textarea v-model="questions[k]['explain']"
                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'masterstudy-lms-learning-management-system') ?>"></textarea>

                    </label>
                    <div class="actions">
                        <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                    </div>
                </div>

            </div>
        </transition>

        <!--Keywords Question-->
        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-keywords"
                 v-if="choice == 'keywords' && questions.length">

                <div class="stm-lms-questions-single_keyword" v-for="(v,k) in questions">
                    <h4><?php esc_html_e('Keyword #'); ?> {{k + 1}}</h4>
                    <input type="text" v-model="questions[k]['text']"/>

                    <textarea v-model="questions[k]['explain']"
                              placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'masterstudy-lms-learning-management-system') ?>"></textarea>
                </div>

            </div>
        </transition>

        <!--Fill the Gap Question-->
        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-fill_the_gap"
                 v-if="choice == 'fill_the_gap' && questions.length">

                <div class="stm-lms-questions-single_fill_the_gap">
                    <h4><?php esc_html_e('Enter text, separate answers with "|" symbol', 'masterstudy-lms-learning-management-system') ?></h4>
                    <p><strong>Example:</strong>
                        Deborah was angry at her son. Her son didn't <strong>|listen|</strong> to her.
                        Her son was 16 years old. Her son <strong>|thought|</strong> he knew everything.
                        Her son <strong>|yelled|</strong> at Deborah.
                    </p>
                    <textarea v-model="questions[0]['text']"
                              placeholder="<?php esc_html_e('Enter text, separate answers with "|" symbol', 'masterstudy-lms-learning-management-system') ?>">

                    </textarea>
                </div>

            </div>
        </transition>

        <div class="stm_lms_answers_container" v-if="choice !== 'true_false' && (choice !== 'fill_the_gap' || questions.length < 1)">
            <div class="stm_lms_answers_container__input">
                <input type="text"
                       v-model="new_answer"
                       v-bind:class="{'shake-it' : isEmpty}"
                       @keydown.enter.prevent="addAnswer()"
                       placeholder="<?php esc_html_e('Enter new Answer', 'masterstudy-lms-learning-management-system'); ?>"/>
            </div>
            <div class="stm_lms_answers_container__submit">
                <a class="button" @click="addAnswer()"><?php esc_html_e('Add Answer') ?></a>
            </div>
        </div>

    </div>
</div>