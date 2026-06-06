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
import { theme } from '../ui/theme';

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
      <Text style={[styles.label, compact && styles.labelCompact]}>{label}</Text>
      <View ref={fieldRef} collapsable={false}>
        <Pressable
          style={[
            styles.field,
            compact && styles.fieldCompact,
            open && styles.fieldOpen,
            disabled && styles.disabled,
          ]}
          onPress={openDropdown}
          disabled={disabled}>
          <Text
            style={[
              selected ? styles.value : styles.placeholder,
              compact && styles.valueCompact,
            ]}
            numberOfLines={1}>
            {selected?.label ?? placeholder}
          </Text>
          <AppIcon
            name={open ? 'chevron-up' : 'chevron-down'}
            size={compact ? 14 : 16}
            color={open ? PRIMARY : theme.muted}
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
                      style={[styles.option, isSelected && styles.optionSelected]}
                      onPress={() => {
                        onChange(item.value);
                        close();
                      }}>
                      <Text
                        style={[
                          styles.optionText,
                          compact && styles.optionTextCompact,
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
  label: { fontSize: 13, fontWeight: '600', color: theme.text, marginBottom: 6 },
  labelCompact: { fontSize: 12, marginBottom: 4 },
  field: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 12,
    backgroundColor: '#fff',
    gap: 8,
  },
  fieldCompact: {
    paddingHorizontal: 10,
    paddingVertical: 10,
    borderRadius: 8,
  },
  fieldOpen: {
    borderColor: PRIMARY,
    backgroundColor: '#f8fbff',
  },
  disabled: { opacity: 0.5 },
  value: { flex: 1, fontSize: 16, color: theme.text },
  valueCompact: { fontSize: 14 },
  placeholder: { flex: 1, fontSize: 16, color: theme.muted },
  modalRoot: { flex: 1 },
  backdrop: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(15, 23, 42, 0.18)',
  },
  dropdown: {
    position: 'absolute',
    backgroundColor: '#fff',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#e2e8f0',
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
    borderBottomColor: '#f1f5f9',
    gap: 10,
  },
  optionSelected: {
    backgroundColor: '#f0f7ff',
  },
  optionText: { flex: 1, fontSize: 15, color: theme.text },
  optionTextCompact: { fontSize: 14 },
  optionActive: { color: PRIMARY, fontWeight: '700' },
});
