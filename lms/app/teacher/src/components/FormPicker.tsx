import React, { useState } from 'react';
import { View, Text, Pressable, Modal, FlatList, StyleSheet } from 'react-native';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

type Option = { label: string; value: string };

export function FormPicker({
  label,
  value,
  options,
  onChange,
  placeholder = 'Select…',
  disabled,
}: {
  label: string;
  value: string;
  options: Option[];
  onChange: (v: string) => void;
  placeholder?: string;
  disabled?: boolean;
}) {
  const [open, setOpen] = useState(false);
  const selected = options.find(o => o.value === value);

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>{label}</Text>
      <Pressable
        style={[styles.field, disabled && styles.disabled]}
        onPress={() => !disabled && setOpen(true)}
        disabled={disabled}>
        <Text style={selected ? styles.value : styles.placeholder}>
          {selected?.label ?? placeholder}
        </Text>
        <Text style={styles.chevron}>▼</Text>
      </Pressable>
      <Modal visible={open} transparent animationType="slide">
        <Pressable style={styles.overlay} onPress={() => setOpen(false)}>
          <View style={styles.sheet}>
            <Text style={styles.sheetTitle}>{label}</Text>
            <FlatList
              data={options}
              keyExtractor={item => item.value}
              renderItem={({ item }) => (
                <Pressable
                  style={styles.option}
                  onPress={() => {
                    onChange(item.value);
                    setOpen(false);
                  }}>
                  <Text style={item.value === value ? styles.optionActive : styles.optionText}>
                    {item.label}
                  </Text>
                </Pressable>
              )}
            />
          </View>
        </Pressable>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: 14 },
  label: { fontSize: 13, fontWeight: '600', color: theme.text, marginBottom: 6 },
  field: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 12,
    backgroundColor: '#fff',
  },
  disabled: { opacity: 0.5 },
  value: { flex: 1, fontSize: 16, color: theme.text },
  placeholder: { flex: 1, fontSize: 16, color: theme.muted },
  chevron: { color: theme.muted, fontSize: 12 },
  overlay: { flex: 1, justifyContent: 'flex-end', backgroundColor: 'rgba(0,0,0,0.4)' },
  sheet: { backgroundColor: '#fff', maxHeight: '50%', borderTopLeftRadius: 16, borderTopRightRadius: 16, padding: 16 },
  sheetTitle: { fontSize: 17, fontWeight: '700', marginBottom: 12 },
  option: { paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  optionText: { fontSize: 16, color: theme.text },
  optionActive: { fontSize: 16, color: PRIMARY, fontWeight: '700' },
});
