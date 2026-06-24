import React from 'react';
import { View, Text, TextInput, StyleSheet } from 'react-native';
import { useThemeColors, textInputStyle } from '../ui/useThemeColors';
import { platformWeight } from '../ui/typography';

export function FormInput({
  label,
  value,
  onChangeText,
  placeholder,
  keyboardType,
  secureTextEntry,
  autoCapitalize,
}: {
  label: string;
  value: string;
  onChangeText: (t: string) => void;
  placeholder?: string;
  keyboardType?: 'default' | 'numeric' | 'email-address' | 'phone-pad';
  secureTextEntry?: boolean;
  autoCapitalize?: 'none' | 'sentences' | 'words' | 'characters';
}) {
  const colors = useThemeColors();

  return (
    <View style={styles.wrap}>
      <Text style={[styles.label, { color: colors.text }]}>{label}</Text>
      <TextInput
        style={textInputStyle(colors)}
        value={value}
        onChangeText={onChangeText}
        placeholder={placeholder}
        placeholderTextColor={colors.muted}
        keyboardType={keyboardType}
        secureTextEntry={secureTextEntry}
        autoCapitalize={autoCapitalize}
        keyboardAppearance="light"
      />
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: 14 },
  label: { fontSize: 13, ...platformWeight('600'), marginBottom: 6 },
});
