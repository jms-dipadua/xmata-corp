(function(  element, blockEditor, blocksConfig  ) {

    const el = element.createElement;
    const __ = wp.i18n.__;

    const availableBlocks = blocksConfig.blockTypes;

    const { SelectControl } = wp.components;

    const knownBlocks = {
        'sociallocker': {
            'title': __('Social Locker', 'bizpanda'),
            'description': __('Hides content inside the block behind a social locker.', 'bizpanda'),
            'keywords': [ 'locker', 'sociallocker', 'social locker', 'social', 'lock' ],
            'transformsFrom': ['bizpanda/signinlocker', 'bizpanda/emaillocker'],
            'icon': <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 992.13 992.13"><path d="M282.61,687.13l-88.25-88.25c-71.66-71.66-71.66-188.26,0-259.93a183.77,183.77,0,0,1,259.94,0l18.26,18.28L490.83,339A183.7,183.7,0,0,1,792.44,534.37l39.15,39.14A235.39,235.39,0,0,0,472.56,286a235.49,235.49,0,0,0-314.72,16.48c-91.79,91.79-91.79,241.16,0,332.95l88.25,88.26Z" fill="#555d66" stroke="#555d66" stroke-miterlimit="10" stroke-width="0.25"/><polygon points="472.56 950.13 319.13 796.7 355.64 760.18 472.56 877.08 730.82 618.82 767.33 655.33 472.56 950.13 472.56 950.13" fill="#555d66" stroke="#555d66" stroke-miterlimit="10" stroke-width="60"/></svg>
        },
        'signinlocker': {
            'title': __('Sign-In Locker', 'bizpanda'),
            'description': __('Hides content inside the block behind a sign-in locker.', 'bizpanda'),
            'keywords': [ 'locker', 'signinlocker', 'signin locker', 'social', 'lock' ],
            'transformsFrom': ['bizpanda/sociallocker', 'bizpanda/emaillocker'],
            'icon': <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 992.13 992.13"><polygon points="482.6 950.55 329.17 797.14 365.69 760.62 482.59 877.52 740.87 619.26 777.38 655.78 482.6 950.55 482.6 950.55" fill="#555d66" stroke="#555d66" stroke-miterlimit="10" stroke-width="60"/><path d="M849.54,583.63L886,547.1,781.71,442.77H626.84l0-35c0.42-4.13,5.11-18.29,8.89-29.67C648,341,666.63,285,666.64,226.54c0-123.93-74-207.21-184.07-207.21S298.63,102.61,298.63,226.54C298.63,285,317.2,341,329.5,378.13c3.77,11.38,8.48,25.54,8.83,29l0,35.59H183.49L79.15,547.1l177,177,36.52-36.53L152.21,547.1l52.68-52.69H390V407.17c0-10.7-4.31-23.72-11.46-45.3-11.25-33.94-28.27-85.24-28.26-135.34C350.26,130.59,401,71,482.57,71S615,130.59,615,226.52c0,50.11-17,101.4-28.3,135.35-7.17,21.57-11.5,34.6-11.5,45.31v87.24H760.32Z" fill="#555d66" stroke="#555d66" stroke-miterlimit="10" stroke-width="0.25"/></svg>
        },
        'emaillocker': {
            'title': __('Email Locker', 'bizpanda'),
            'description': __('Hides content inside the block behind an email locker.', 'bizpanda'),
            'keywords': [ 'locker', 'emaillocker', 'email locker', 'optin', 'opt-in', 'lock' ],
            'transformsFrom': ['bizpanda/sociallocker', 'bizpanda/signinlocker'],
            'icon': <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 992.13 992.13"><path d="M231.37,686.2l-91.73-91.73,255.85,0H586.16v-139H842.06l-89.44,89.44,36.53,36.52L940.9,429.61,560.35,49.07,40.77,568.65,194.85,722.72Zm354.8-538.28L842,403.78l-255.88,0V147.93Zm-51.63,0V542.81l-394.89,0Z" fill="#555d66" stroke="#555d66" stroke-miterlimit="10" stroke-width="0.25"/><polygon points="421.33 949.21 267.89 795.75 304.4 759.25 421.33 876.16 679.58 617.89 716.09 654.4 421.33 949.21 421.33 949.21" fill="#555d66" stroke="#555d66" stroke-miterlimit="10" stroke-width="60"/></svg>
        }
    };

    /**
     * Locker Select
    */
    class LockerSelect extends wp.element.Component {

        constructor() {
            super(...arguments);

            this.state = {
                isLoading: true,
                selectedId: this.props.lockerId ? parseInt( this.props.lockerId ) : 0,
                options: []
            };

            this.handleSelectChange = this.handleSelectChange.bind(this);
        }

        componentDidMount() {
            const self = this;

            const request = jQuery.ajax(window.ajaxurl, {
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'get_opanda_lockers',
                    shortcode: self.props.shortcode
                }
            });

            request.done(function( data ){

                const options = [];

                let hasSelected = false;
                let defaultLocker = false;

                for ( let locker of data ) {

                    if ( self.state.selectedId && self.state.selectedId === parseInt( locker.id ) ) {
                        hasSelected = true;
                    }

                    const item = { label: locker.title, value: locker.id, shortcode: locker.shortcode };

                    if ( !defaultLocker ) defaultLocker = item;
                    options.push(item);
                }

                if ( !hasSelected && defaultLocker )  {
                    console.log(defaultLocker);
                    self.props.onChange(defaultLocker);
                }

                self.setState({
                    isLoading: false,
                    options: options,
                    selectedId: hasSelected ? self.state.selectedId : defaultLocker.value
                });
            });
        }

        handleSelectChange( lockerId ) {

            let option = null;
            for ( let optionItem of this.state.options ) {
                if ( parseInt( optionItem.value ) === parseInt( lockerId ) ) {
                    option = optionItem;

                    this.setState({
                        selectedId: lockerId ? parseInt( lockerId ) : 0
                    });

                    break;
                }
            }

            this.props.onChange( option );
        }

        render() {
            const self = this;

            let options = this.state.options;

            if ( this.state.isLoading ) {
                options = [{label: __('Loading...', 'bizpanda'), value: 0}];
            }

            const hasLockers = options.length > 0;

            if ( !hasLockers ) {
                options = [{label: __('[ - empty - ]', 'bizpanda'), value: 0}];
            }

            const hasEditableItem = ( !this.state.isLoading && hasLockers && this.state.selectedId ) ? true : false;
            const hasAddAbility = !this.state.isLoading;

            return (
              <>
                  <SelectControl
                      className={"onp-locker-select-wrap"}
                      label={self.props.label + ':'}
                      onChange={this.handleSelectChange}
                      value={self.props.lockerId}
                      options={options}
                  />
                  {
                      ( hasAddAbility || hasEditableItem ) &&
                      <div>|</div>
                  }
                  {
                      hasEditableItem &&
                      <a href={blocksConfig.urlEditUrl.replace('{0}', this.state.selectedId)} target="_blank" className="button onp-button">{__('Edit', 'bizpanda')}</a>
                  }
                  {
                      hasAddAbility &&
                      <a href={blocksConfig.urlCreateNew} target="_blank" className="button onp-button">{__('Add', 'bizpanda')}</a>
                  }
              </>
            );
        }
    }

    for ( let pluginBlockType of availableBlocks ) {
        if ( !knownBlocks[pluginBlockType] ) continue;

        const shortcode = pluginBlockType;
        const blockName = 'bizpanda/' + pluginBlockType;
        const blockTitle = knownBlocks[pluginBlockType].title;
        const blockDescription = knownBlocks[pluginBlockType].description;
        const blockIcon = knownBlocks[pluginBlockType].icon;
        const blockKeywords = knownBlocks[pluginBlockType].keywords;
        const blockTransformsFrom = knownBlocks[pluginBlockType].transformsFrom;

        wp.blocks.registerBlockType(blockName, {
            title: blockTitle,
            description: blockDescription,
            icon: blockIcon,
            category: 'widgets',
            keywords: blockKeywords,

            attributes: {
                id: {
                    type: 'number'
                },
            },

            transforms: {
                from: [
                    {
                        type: 'block',
                        blocks: blockTransformsFrom,
                        transform: function ( attributes ) {
                            return wp.blocks.createBlock( blockName, {...attributes});
                        },
                    },
                ]
            },

            edit: function(props) {

                const elements = [];

                // if selected, shows the settings

                if ( props.isSelected ) {

                    const onChange = (option) => {

                        console.log( option );

                        props.setAttributes({
                            id: ( option && option.value ) ? parseInt( option.value ) : null
                        });
                    };

                    const configWrap = (
                        <div className="onp-config-wrap">
                            <LockerSelect
                                shortcode={shortcode}
                                label={blockTitle}
                                onChange={onChange}
                                lockerId={props.attributes.id}>
                            </LockerSelect>
                        </div>
                    );

                    elements.push(configWrap);
                }

                const previewWrap = (
                    <div className="onp-preview-wrap">
                        <div className="onp-top-bracket"></div>
                        <blockEditor.InnerBlocks />
                        <div className="onp-bottom-bracket"></div>
                    </div>
                )

                elements.push(previewWrap);

                return (
                    <div className="onp-locker">{elements}</div>
                );
            },

            save: function(props) {

                return (
                    <div className="onp-locker-block">
                        <blockEditor.InnerBlocks.Content />
                    </div>
                );
            }
        });

    }

})(
    window.wp.element,
    window.wp.blockEditor,
    window.__bizpanda_locker_blocks
);
