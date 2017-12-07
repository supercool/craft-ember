(function ($) {

    Craft.InfoHUD = Garnish.Base.extend({
        $element: null,
        hud: null,
        action: 'click',

        init: function ($element) {
            this.$element = $element;

            var action = this.$element.data('hud-action');
            if (action) {
                this.action = action;
            }

            this.addListener(this.$element, this.action, 'showHud');
        },

        showHud: function () {
            if (!this.hud) {
                this.hud = this.createHud()
            } else {
                this.hud.show();
            }
        },
        createHud: function () {
            var hudClass = this.$element.data('hud-class');
            if (hudClass) {
                hudClass = ' '+hudClass;
            }

            return new Garnish.HUD(this.$element, this.getContents(), {
                hudClass: 'hud tooltip-hud'+hudClass,
                closeOtherHUDs: false
            });
        },
        getContents: function () {
            return this.$element.html();
        }
    });

})(jQuery);