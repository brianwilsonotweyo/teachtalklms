<div class="stm-curriculum" v-bind:class="{\'loading\': loading}">

    <v-select v-model="search"
              label="title"
              :options="options"
              @search="onSearch">
    </v-select>

    <ul class="stm-lms-autocomplete">
        <li v-for="(item, index) in items">
            <span v-html="item.title"></span>
            <i class="lnr lnr-cross" @click="removeItem(index)"></i>
        </li>
    </ul>

</div>