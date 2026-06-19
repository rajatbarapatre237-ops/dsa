import { useColorScheme } from 'react-native';
import { TextStyle } from 'react-native';
import { theme } from './theme';
import { androidInputStyle } from './typography';

export function useThemeColors() {
  const isDark = useColorScheme() === 'dark';

  return {
    ...theme,
    isDark,
    surface: isDark ? '#1e293b' : '#ffffff',
    inputBg: isDark ? '#0f172a' : '#ffffff',
    inputText: isDark ? '#f8fafc' : '#0f172a',
    inputBorder: isDark ? '#475569' : '#e2e8f0',
    card: isDark ? '#1e293b' : '#ffffff',
    bg: isDark ? '#0f172a' : '#eef3f9',
    text: isDark ? '#f8fafc' : '#0f172a',
    muted: isDark ? '#94a3b8' : '#64748b',
    border: isDark ? '#334155' : '#e2e8f0',
    primarySoft: isDark ? '#1e3a5f' : '#e8f2fc',
    fieldOpenBg: isDark ? '#1e3a5f' : '#f8fbff',
    dropdownBg: isDark ? '#1e293b' : '#ffffff',
    optionSelectedBg: isDark ? '#1e3a5f' : '#f0f7ff',
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
