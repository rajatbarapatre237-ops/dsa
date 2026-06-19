import React, { useCallback, useRef, useState } from 'react';
import {
  View,
  Text,
  Pressable,
  Modal,
  ScrollView,
  StyleSheet,
  Dimensions,
  LayoutRectangle,
} from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { useThemeColors } from '../ui/useThemeColors';

type Option = { label: string; value: string };

const DROPDOWN_MAX_HEIGHT = 220;
const DROPDOWN_GAP = 4;

export function FormPicker({
  label,
  value,
  options,
  onChange,
  placeholder = 'Select…',
  disabled,
  compact,
}: {
  label: string;
  value: string;
  options: Option[];
  onChange: (v: string) => void;
  placeholder?: string;
  disabled?: boolean;
  compact?: boolean;
}) {
  const colors = useThemeColors();
  const fieldRef = useRef<View>(null);
  const [open, setOpen] = useState(false);
  const [anchor, setAnchor] = useState<LayoutRectangle | null>(null);
  const [openAbove, setOpenAbove] = useState(false);
  const selected = options.find(o => o.value === value);

  const close = useCallback(() => {
    setOpen(false);
    setAnchor(null);
  }, []);

  const openDropdown = useCallback(() => {
    if (disabled) {
      return;
    }

    fieldRef.current?.measureInWindow((x, y, width, height) => {
      const screenHeight = Dimensions.get('window').height;
      const spaceBelow = screenHeight - (y + height) - DROPDOWN_GAP;
      const spaceAbove = y - DROPDOWN_GAP;
      const shouldOpenAbove =
        spaceBelow < Math.min(DROPDOWN_MAX_HEIGHT, options.length * 44) &&
        spaceAbove > spaceBelow;

      setOpenAbove(shouldOpenAbove);
      setAnchor({ x, y, width, height });
      setOpen(true);
    });
  }, [disabled, options.length]);

  const screenHeight = Dimensions.get('window').height;
  const dropdownStyle = anchor
    ? openAbove
      ? {
          bottom: screenHeight - anchor.y + DROPDOWN_GAP,
          maxHeight: Math.min(DROPDOWN_MAX_HEIGHT, anchor.y - 16),
        }
      : {
          top: anchor.y + anchor.height + DROPDOWN_GAP,
          maxHeight: Math.min(
            DROPDOWN_MAX_HEIGHT,
            screenHeight - anchor.y - anchor.height - DROPDOWN_GAP - 16,
          ),
        }
    : null;

  return (
    <View style={[styles.wrap, compact && styles.wrapCompact]}>
      <Text style={[styles.label, compact && styles.labelCompact, { color: colors.text }]}>{label}</Text>
      <View ref={fieldRef} collapsable={false}>
        <Pressable
          style={[
            styles.field,
            compact && styles.fieldCompact,
            {
              borderColor: open ? PRIMARY : colors.inputBorder,
              backgroundColor: open ? colors.fieldOpenBg : colors.inputBg,
            },
            disabled && styles.disabled,
          ]}
          onPress={openDropdown}
          disabled={disabled}>
          <Text
            style={[
              selected ? styles.value : styles.placeholder,
              compact && styles.valueCompact,
              { color: selected ? colors.text : colors.muted },
            ]}
            numberOfLines={1}>
            {selected?.label ?? placeholder}
          </Text>
          <AppIcon
            name={open ? 'chevron-up' : 'chevron-down'}
            size={compact ? 14 : 16}
            color={open ? PRIMARY : colors.muted}
          />
        </Pressable>
      </View>

      <Modal visible={open} transparent animationType="fade" onRequestClose={close}>
        <View style={styles.modalRoot}>
          <Pressable style={styles.backdrop} onPress={close} />
          {anchor && dropdownStyle ? (
            <View
              style={[
                styles.dropdown,
                {
                  left: anchor.x,
                  width: anchor.width,
                  backgroundColor: colors.dropdownBg,
                  borderColor: colors.border,
                  ...dropdownStyle,
                },
              ]}>
              <ScrollView
                keyboardShouldPersistTaps="handled"
                nestedScrollEnabled
                bounces={options.length > 5}>
                {options.map((item, index) => {
                  const isSelected = item.value === value;
                  return (
                    <Pressable
                      key={`${item.value}-${index}`}
                      style={[
                        styles.option,
                        { borderBottomColor: colors.border },
                        isSelected && { backgroundColor: colors.optionSelectedBg },
                      ]}
                      onPress={() => {
                        onChange(item.value);
                        close();
                      }}>
                      <Text
                        style={[
                          styles.optionText,
                          compact && styles.optionTextCompact,
                          { color: isSelected ? PRIMARY : colors.text },
                          isSelected && styles.optionActive,
                        ]}
                        numberOfLines={2}>
                        {item.label}
                      </Text>
                      {isSelected ? (
                        <AppIcon name="checkmark" size={16} color={PRIMARY} />
                      ) : null}
                    </Pressable>
                  );
                })}
              </ScrollView>
            </View>
          ) : null}
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: 14, zIndex: 1 },
  wrapCompact: { marginBottom: 10 },
  label: { fontSize: 13, fontWeight: '600', marginBottom: 6 },
  labelCompact: { fontSize: 12, marginBottom: 4 },
  field: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 12,
    gap: 8,
  },
  fieldCompact: {
    paddingHorizontal: 10,
    paddingVertical: 10,
    borderRadius: 8,
  },
  disabled: { opacity: 0.5 },
  value: { flex: 1, fontSize: 16 },
  valueCompact: { fontSize: 14 },
  placeholder: { flex: 1, fontSize: 16 },
  modalRoot: { flex: 1 },
  backdrop: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(15, 23, 42, 0.18)',
  },
  dropdown: {
    position: 'absolute',
    borderRadius: 10,
    borderWidth: 1,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.12,
    shadowRadius: 16,
    elevation: 10,
    overflow: 'hidden',
  },
  option: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 14,
    paddingVertical: 12,
    borderBottomWidth: StyleSheet.hairlineWidth,
    gap: 10,
  },
  optionText: { flex: 1, fontSize: 15 },
  optionTextCompact: { fontSize: 14 },
  optionActive: { fontWeight: '700' },
});
