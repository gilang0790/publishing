Vue.component('app-select2', {
    props: ['options', 'value', 'placeholder', 'targettext', 'defaulttext'],
    template: '<select><slot></slot></select>',
    mounted: function () {
        var vm = this;
        //console.log(this.options);
        $(this.$el)
            // init select2
            .select2({data: this.options, placeholder: this.placeholder})
            .val(this.value)
            .trigger('change')
            // emit event on change.
            .on('change', function () {
                $(vm.targettext).text( this.value ? vm.options.find(x => x.id == this.value).text : vm.defaulttext);
                vm.$emit('input', this.value);
            });
            
        $(vm.targettext).text( this.value ? vm.options.find(x => x.id == this.value).text : vm.defaulttext);
        
        //setTimeout(function() { $(vm.$el).val(vm.value).trigger('select2:select'); }, 500);
        $(window).on('load', function(){
            $(vm.$el).val(vm.value).trigger('select2:select');
        });
        
        
    },
    watch: {
        value: function (value) {
            // update value
            $(this.$el)
                .val(value)
                .trigger('change');
        },
        options: function (options) {
            // update options
            $(this.$el).empty().select2({data: options, placeholder: this.placeholder});
        },
        placeholder: function (placeholder) {
            $(this.$el).empty().select2({data: options, placeholder: this.placeholder});
        }
    },
    destroyed: function () {
        $(this.$el).off().select2('destroy');
    }
});

Vue.component('app-depdrop', {
    props: ['value', 'depends', 'url', 'placeholder'],
    template: "<select></select>",
    mounted: function() {
        var vm = this;
        $(this.$el).depdrop({
            depends: this.depends,
            placeholder: this.placeholder,
            url: this.url
        })
        .val(this.value)
        .trigger('change')
        .on('change', function(){
            vm.$emit('input', this.value);
        })
        .on('depdrop:afterChange', function(event, id, value, jqXHR, textStatus){
            if (Object.keys($(vm.$el).depdrop('getAjaxResults')).length > 0)
            {
                if ($(vm.$el).data('depdrop-selected'))
                {
                    var val = $(vm.$el).data('depdrop-selected');
                    $(vm.$el).val(val).change();
                    
                    $(vm.$el).removeData('depdrop-selected');
                } else
                {
                    $(vm.$el).val(vm.value).change();
                }
            }
        })
        .on('depdrop:error', function(event, id, value, jqXHR, textStatus, errorThrown) {
            $('#'+id).trigger('select2:select');
            $(vm.$el).data('depdrop-selected', vm.value);
        });
    },
    watch: {
        value: function (value) {
            $(this.$el).val(value)
                .trigger('change');
        },
        depends: function (options) {
             $(this.$el).depdrop({
                depends: this.depends,
                placeholder: this.placeholder,
                url: this.url
            });
        },
        url: function (options) {
             $(this.$el).depdrop({
                depends: this.depends,
                placeholder: this.placeholder,
                url: this.url
            });
        },
        placeholder: function (options) {
             $(this.$el).depdrop({
                depends: this.depends,
                placeholder: this.placeholder,
                url: this.url
            });
        }
    },
    destroyed: function () {
        $(this.$el).off().depdrop('destroy');
    }
});

Vue.component('app-input', {
    props: ['value'],
    template: '<input type="text" class="form-control" v-bind:value="value">',
    mounted: function () {
        var vm = this;
        $(this.$el)
            // emit event on change.
            .on('change', function () {
                vm.$emit('input', this.value);
            });
    }
});

Vue.component('app-date', {
    props: ['value', 'format', 'todayBtn', 'language', 'id' ,'name', 'autocomplete'],
    template: `<div v-bind:id="id+'-kvdate'" class="input-group date">
<span class="input-group-addon kv-date-calendar" title="" data-original-title="Select Date"><i class="glyphicon glyphicon-calendar"></i></span>
<span class="input-group-addon kv-date-remove" title="" data-original-title="Clear Field"><i class="glyphicon glyphicon-remove"></i></span>
<input v-bind:name="name" v-bind:id="id" data-datepicker-type="2" type="text" class="form-control krajee-datepicker" v-model="value" v-bind:data-datepicker-source="id+'-kvdate'" v-bind:autocomplete="autocomplete"></div>`,
    mounted: function () {
        var vm = this;
        $(this.$el)
            // init select2
            .kvDatepicker({format: this.format, todayBtn: this.todayBtn, language: this.language})
            .find('.kv-date-remove').click(function(){
                vm.value = '';
            });
        $(this.$el).find('input')
        .val(this.value)
        .trigger('change')
        .change(function(){
            vm.value = this.value;
            vm.$emit('input', this.value);
        })
    },
    watch: {
        value: function (value) {
            // update value
            $(this.$el)
                .find('input')
                .val(value)
                .trigger('change');
        },
        format: function (value) {
           $(this.$el).kvDatepicker({format: this.format, todayBtn: this.todayBtn, language: this.language});
        },
        todayBtn: function (value) {
           $(this.$el).kvDatepicker({format: this.format, todayBtn: this.todayBtn, language: this.language});
        },
        language: function (value) {
           $(this.$el).kvDatepicker({format: this.format, todayBtn: this.todayBtn, language: this.language});
        }
    },
    destroyed: function () {
        $(this.$el).off().kvDatepicker('destroy');
    }
});

Vue.component('app-percent', {
    props: ['value'],
    template: '<input data-inputmask-allowMinus="false" data-inputmask-max="100" class="form-control text-right input-decimal" v-bind:value="value">',
    mounted: function () {
        var vm = this;
        $(this.$el)
            // emit event on change.
            .on('change', function () {
                vm.$emit('input', this.value);
            });
    }
});

Vue.component('app-integer', {
    props: ['value'],
    template: '<input class="form-control text-right input-integer" v-bind:value="value">',
    mounted: function () {
        var vm = this;
        $(this.$el)
            // emit event on change.
            .on('change', function () {
                vm.$emit('input', this.value);
            });
    }
});

Vue.component('app-decimal', {
    props: ['value', 'digits'],
    template: '<input class="form-control text-right" v-bind:value="value">',
    mounted: function () {
        var vm = this;
        if (!this.digits) this.digits = 2;
        $(this.$el)
            // emit event on change.
            .on('change', function () {
                if ($(this).data('dontemitonchange')) return;
                vm.$emit('input', this.value);
            })
            .inputmask({
                alias: 'decimal',
                digits: this.digits,
                digitsOptional: false,
                groupSeparator: '.',
                allowMinus: true,
                prefix: ' ',
                radixPoint: ',',
                autoGroup: true,
                removeMaskOnSubmit: false,
                rightAlign: false
            });
    }
});

Vue.component('app-decimal-auto', {
    props: ['value'],
    template: '<input class="form-control input-vue-decimal text-right" v-bind:value="value">',
    mounted: function () {
        var vm = this;
        $(this.$el)
            // emit event on change.
            .on('change', function () {
                if ($(this).data('dontemitonchange')) return;
                vm.$emit('input', this.value);
            })
            .inputmask({
                alias: 'decimal',
                digits: '4',
                rightAlign: true,
                groupSeparator: '.',
                radixPoint: ',',
                autoGroup: true
            });

        this.value = this.value.toDecimal();
    }
});

Vue.component('app-radio', {
    props: ['value'],
    template: '<input type="radio" v-bind:value="value">',
    mounted: function () {
        var vm = this;
        $(this.$el)
            // emit event on change.
            .on('change', function () {
                vm.$emit('input', this.value);
            });
    }
});
