import { TextStyle } from 'react-native';
import { theme } from './theme';
import { androidInputStyle } from './typography';

export function useThemeColors() {
  return {
    ...theme,
    isDark: false,
    surface: '#ffffff',
    inputBg: '#ffffff',
    inputText: '#0f172a',
    inputBorder: '#e2e8f0',
    card: '#ffffff',
    bg: '#eef3f9',
    text: '#0f172a',
    muted: '#64748b',
    border: '#e2e8f0',
    primarySoft: '#e8f2fc',
    fieldOpenBg: '#f8fbff',
    dropdownBg: '#ffffff',
    optionSelectedBg: '#f0f7ff',
  };
}

export function textInputStyle(colors: ReturnType<typeof useThemeColors>): TextStyle {
  return {
    borderWidth: 1,
    borderColor: colors.inputBorder,
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 16,
    color: colors.inputText,
    backgroundColor: colors.inputBg,
    ...androidInputStyle,
  };
}
