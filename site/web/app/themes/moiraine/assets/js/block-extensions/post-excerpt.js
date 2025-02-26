(function(wp) {
    const { __ } = wp.i18n;
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl } = wp.components;
    const { createElement } = wp.element;

    // Add attributes to the post-excerpt block
    function addAttributes(settings, name) {
        if (name !== 'core/post-excerpt') {
            return settings;
        }

        return {
            ...settings,
            attributes: {
                ...settings.attributes,
                linkToPost: {
                    type: 'boolean',
                    default: false
                },
                underlineLink: {
                    type: 'boolean',
                    default: false
                }
            }
        };
    }

    // Add inspector controls to the post-excerpt block
    const withInspectorControls = createHigherOrderComponent(function(BlockEdit) {
        return function(props) {
            if (props.name !== 'core/post-excerpt') {
                return createElement(BlockEdit, props);
            }

            const { attributes, setAttributes } = props;
            const { linkToPost, underlineLink } = attributes;

            return createElement(
                Fragment,
                {},
                createElement(BlockEdit, props),
                createElement(
                    InspectorControls,
                    {},
                    createElement(
                        PanelBody,
                        {
                            title: __('Link Settings', 'moiraine'),
                            initialOpen: false,
                            className: "moiraine-excerpt-link-settings"
                        },
                        createElement(
                            ToggleControl,
                            {
                                label: __('Link excerpt to post', 'moiraine'),
                                checked: !!linkToPost,
                                onChange: function() {
                                    setAttributes({ linkToPost: !linkToPost });
                                }
                            }
                        ),
                        linkToPost && createElement(
                            ToggleControl,
                            {
                                label: __('Underline link', 'moiraine'),
                                checked: !!underlineLink,
                                onChange: function() {
                                    setAttributes({ underlineLink: !underlineLink });
                                }
                            }
                        )
                    )
                )
            );
        };
    }, 'withInspectorControls');

    // Modify the save function to conditionally wrap the excerpt in a link
    const modifySaveElement = function(element, blockType, attributes) {
        if (blockType.name !== 'core/post-excerpt') {
            return element;
        }

        if (!attributes.linkToPost) {
            return element;
        }

        // We're in the editor, so we need to add a class to simulate the link
        // The actual linking happens on the PHP side
        const className = 'moiraine-linked-excerpt' + (attributes.underlineLink ? ' has-underline' : ' no-underline');
        
        // Add our custom class to the existing element
        if (element && element.props && element.props.className) {
            return wp.element.cloneElement(element, {
                className: element.props.className + ' ' + className
            });
        } else {
            return wp.element.cloneElement(element, {
                className: className
            });
        }
    };

    // Register our filters
    addFilter(
        'blocks.registerBlockType',
        'moiraine/post-excerpt-link-attributes',
        addAttributes
    );

    addFilter(
        'editor.BlockEdit',
        'moiraine/post-excerpt-link-controls',
        withInspectorControls
    );

    addFilter(
        'blocks.getSaveElement', 
        'moiraine/post-excerpt-link-output',
        modifySaveElement
    );
})(window.wp);
