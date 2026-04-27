(function (Handsontable) {
    const BaseEditor = Handsontable.editors.BaseEditor;

    const EDITOR_TYPE = 'select2';
    const EDITOR_STATE = Object.freeze({
        VIRGIN: 'STATE_VIRGIN',
        EDITING: 'STATE_EDITING',
        WAITING: 'STATE_WAITING',
        FINISHED: 'STATE_FINISHED'
    });

    const SHORTCUTS_GROUP_NAVIGATION = 'editorManager.navigation';
    const SHORTCUTS_GROUP = 'select2Editor';
    const SHORTCUTS_GROUP_EDITOR = 'baseEditor';

    class Select2Editor extends BaseEditor {
        static get EDITOR_TYPE() {
            return EDITOR_TYPE;
        }

        createElements() {
            this.SELECT_PARENT = $('<div></div>');
            this.SELECT = $('<select id="handsontable-select2-editor" style="width: 100%;"></select>');

            this.SELECT_PARENT.css({ 'position': `absolute` });
            this.SELECT_PARENT.css({ 'margin': `0px` });
            this.SELECT_PARENT.css({ 'padding': `0px` });
            // this.SELECT_PARENT.css({ 'overflow': `hidden` });

            this.SELECT_PARENT.hide();
            this.SELECT_PARENT.append(this.SELECT);

            $(this.hot.rootElement).append(this.SELECT_PARENT);
        }

        refreshDimensions(force = false) {
            if (this.state !== EDITOR_STATE.EDITING && !force) {
                return;
            }

            this.TD = this.getEditedCell();

            if (!this.TD) {
                if (!force) {
                    this.close();
                }

                return;
            }

            const { top, start, height, maxHeight, width, maxWidth } = this.getEditedCellRect();
            const rtl = this.hot.isRtl() ? 'right' : 'left';

            this.SELECT_PARENT.css({ 'top': `${top}px` });
            this.SELECT_PARENT.css({ [rtl]: `${start}px` });
            this.SELECT_PARENT.css({ 'height': `${height}px` });
            this.SELECT_PARENT.css({ 'max-height': `${maxHeight}px` });
            this.SELECT_PARENT.css({ 'width': `${width}px` });
            this.SELECT_PARENT.css({ 'max-width': `${maxWidth}px` });
        }

        refreshValue() {
            const physicalRow = this.hot.toPhysicalRow(this.row);
            const sourceData = this.hot.getSourceDataAtCell(physicalRow, this.prop);

            this.originalValue = sourceData;

            this.setValue(sourceData);
            this.refreshDimensions();
        }

        registerHooks() {
            this.addHook('afterScrollHorizontally', () => this.refreshDimensions());
            this.addHook('afterScrollVertically', () => this.refreshDimensions());
            this.addHook('afterColumnResize', () => this.refreshDimensions());
            this.addHook('afterRowResize', () => this.refreshDimensions());

            this.SELECT_PARENT.on('focusout', () => {
                if (!this.SELECT.data('select2').hasFocus()) {
                    this.finishEditing(false)
                }
            });
        }

        clearHooks() {
            super.clearHooks();

            this.SELECT_PARENT.off('focusout');
        }

        registerShortcuts() {
            const shortcutManager = this.hot.getShortcutManager();
            const editorContext = shortcutManager.getContext('editor');
            const gridContext = shortcutManager.getContext('grid');
            const contextConfig = {
                runOnlyIf: () => this.hot.getSelected() != null,
                group: SHORTCUTS_GROUP,
            };

            editorContext.addShortcuts([{
                keys: [
                    ['Tab'],
                    ['Shift', 'Tab'],
                ],
                forwardToContext: gridContext,
                callback: () => { },
            }, {
                keys: [['Esc']],
                callback: () => {
                    this.close();
                    return false;
                },
            }, {
                keys: [['ArrowUp']],
                callback: () => {
                    return false;
                },
            }, {
                keys: [['ArrowDown']],
                callback: () => {
                    return false;
                }
            }], contextConfig);
        }

        unregisterShortcuts() {
            const shortcutManager = this.hot.getShortcutManager();
            const editorContext = shortcutManager.getContext('editor');

            editorContext.removeShortcutsByGroup(SHORTCUTS_GROUP_NAVIGATION);
            editorContext.removeShortcutsByGroup(SHORTCUTS_GROUP);
            editorContext.removeShortcutsByGroup(SHORTCUTS_GROUP_EDITOR);
        }

        destroy() {
            this.clearHooks();
        }

        constructor(hotInstance) {
            super(hotInstance);

            this.createElements();
            this.registerHooks();
            this.hot.addHookOnce('afterDestroy', () => this.destroy());
        }

        prepare(row, col, prop, td, value, cellProperties) {
            const previousState = this.state;

            super.prepare(row, col, prop, td, value, cellProperties);

            if (!cellProperties.readOnly) {
                this._propID = cellProperties.dataID;
                this._propText = cellProperties.dataText;

                this.refreshDimensions(true);

                if (cellProperties.allowInvalid) {
                    this.SELECT.val('');
                }

                if (this.SELECT.data('select2') != null) {
                    this.SELECT.data('select2').destroy();
                }

                this.SELECT.select2(cellProperties.select2Options);

                if (previousState !== EDITOR_STATE.FINISHED) {
                    this.SELECT_PARENT.hide();
                }
            }
        }

        getSelectedItem() {
            const selectedOption = this.SELECT.find(':selected');

            return {
                id: selectedOption.val(),
                text: selectedOption.text()
            };
        }

        getValue() {
            const selectedItem = this.getSelectedItem();

            let text = this.SELECT.val();

            if (this._propID != null && this._propID != '') {
                const physicalRow = this.hot.toPhysicalRow(this.row);
                this.hot.setSourceDataAtCell(physicalRow, this._propID, selectedItem.id);

                text = selectedItem.text;
            } else if (this._propText != null && this._propText != '') {
                const physicalRow = this.hot.toPhysicalRow(this.row);
                this.hot.setSourceDataAtCell(physicalRow, this._propText, selectedItem.text);
            }

            return text;
        }

        setValue(newValue) {
            let id = newValue;
            let text = null;

            if (newValue != null && this._propID != null && this._propID != '') {
                const physicalRow = this.hot.toPhysicalRow(this.row);
                const dataID = this.hot.getSourceDataAtCell(physicalRow, this._propID);

                if (dataID != null && dataID != '') {
                    id = dataID;
                    text = newValue;
                }
            } else if (newValue != null && this._propText != null && this._propText != '') {
                const physicalRow = this.hot.toPhysicalRow(this.row);
                const dataText = this.hot.getSourceDataAtCell(physicalRow, this._propText);

                if (dataText != null) {
                    text = dataText;
                }
            }

            if (id != null && text != null) {
                this.SELECT.select2('trigger', 'select', { data: { id: id, text: text } });
            } else {
                this.SELECT.val(id).trigger('change');
            }
        }

        open() {
            this.refreshDimensions();
            this.SELECT_PARENT.show();
            this.SELECT.data('select2').open();
            this.hot.getShortcutManager().setActiveContextName('editor');
            this.registerShortcuts();
        }

        close() {
            const rootElement = $(this.hot.rootElement);
            const activeElement = $(this.hot.rootDocument.activeElement);
            const isThisHotChild = rootElement.has(activeElement).length > 0;

            if (isThisHotChild) {
                this.hot.listen();
            }

            this.SELECT.data('select2').close();
            this.SELECT_PARENT.hide();
            this.unregisterShortcuts();
        }

        focus() {
            this.SELECT_PARENT.focus();
        }

        beginEditing(newInitialValue, event) {
            if (this.state !== EDITOR_STATE.VIRGIN) {
                return;
            }

            this.SELECT.val('');
            super.beginEditing(newInitialValue, event);
        }
    }

    Handsontable.editors.Select2Editor = Select2Editor;
    Handsontable.editors.registerEditor(EDITOR_TYPE, Select2Editor);
})(Handsontable);