import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  FlatList,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Keyboard,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { sendChatbotMessage } from '../api/ApiClient';
import { colors } from '../theme/colors';

export default function ChatBotScreen({ navigation }) {
  const [messages, setMessages] = useState([
    {
      id: '1',
      text: 'Hello! 🍽️ Welcome to Sip & Bite Assistant. Ask me about our delicious food menu, pizzas, burgers, chef specials, or table reservations!',
      sender: 'bot',
    },
  ]);
  const [inputText, setInputText] = useState('');
  const [loading, setLoading] = useState(false);
  const flatListRef = useRef();

  useEffect(() => {
    const showSub = Keyboard.addListener('keyboardDidShow', () => {
      setTimeout(() => {
        flatListRef.current?.scrollToEnd({ animated: true });
      }, 50);
    });
    return () => showSub.remove();
  }, []);

  const handleSend = async () => {
    if (!inputText.trim()) return;

    const userMsg = { id: Date.now().toString(), text: inputText.trim(), sender: 'user' };
    setMessages((prev) => [...prev, userMsg]);
    setInputText('');
    setLoading(true);

    try {
      const res = await sendChatbotMessage(userMsg.text);
      if (res.data?.success) {
        const botMsg = { id: (Date.now() + 1).toString(), text: res.data.reply, sender: 'bot' };
        setMessages((prev) => [...prev, botMsg]);
      } else {
        const botMsg = {
          id: (Date.now() + 1).toString(),
          text: res.data?.reply || 'I am brewing some thoughts! Could you ask that again?',
          sender: 'bot',
        };
        setMessages((prev) => [...prev, botMsg]);
      }
    } catch (error) {
      const serverReply = error.response?.data?.reply;
      const fallbackText =
        serverReply || error.message || 'Unable to connect to café server. Please ensure backend is running.';
      const botMsg = { id: (Date.now() + 1).toString(), text: fallbackText, sender: 'bot' };
      setMessages((prev) => [...prev, botMsg]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="close" size={24} color="#FFFFFF" />
        </Pressable>
        <View style={styles.headerTitleContainer}>
          <Ionicons name="sparkles" size={18} color={colors.accent} style={{ marginRight: 6 }} />
          <Text style={styles.headerTitle}>Sip & Bite AI</Text>
        </View>
        <View style={{ width: 32 }} />
      </View>

      <FlatList
        ref={flatListRef}
        data={messages}
        keyExtractor={(item) => item.id}
        contentContainerStyle={styles.messageList}
        keyboardShouldPersistTaps="handled"
        onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: true })}
        renderItem={({ item }) => (
          <View
            style={[
              styles.messageBubble,
              item.sender === 'user' ? styles.userBubble : styles.botBubble,
            ]}
          >
            <Text
              style={[
                styles.messageText,
                item.sender === 'user' ? styles.userText : styles.botText,
              ]}
            >
              {item.text}
            </Text>
          </View>
        )}
      />

      {loading && (
        <View style={styles.typingIndicator}>
          <ActivityIndicator size="small" color={colors.primary} style={{ marginRight: 8 }} />
          <Text style={styles.typingText}>Sip & Bite AI is typing a response...</Text>
        </View>
      )}

      <View style={styles.inputArea}>
        <TextInput
          style={styles.input}
          placeholder="Ask about dishes, burgers, pizzas, reservations..."
          placeholderTextColor={colors.textPlaceholder}
          value={inputText}
          onChangeText={setInputText}
          onSubmitEditing={handleSend}
          blurOnSubmit={false}
          returnKeyType="send"
        />
        <Pressable style={styles.sendButton} onPress={handleSend} disabled={loading}>
          <Ionicons name="send" size={18} color="#FFFFFF" />
        </Pressable>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  headerBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? ((StatusBar.currentHeight || 28) + 8) : 12,
    paddingBottom: 14,
    backgroundColor: colors.primaryDark,
    elevation: 4,
  },
  backButton: { width: 36, height: 36, justifyContent: 'center' },
  headerTitleContainer: { flexDirection: 'row', alignItems: 'center' },
  headerTitle: {
    fontSize: 18,
    fontWeight: '900',
    color: '#FFFFFF',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },

  messageList: { padding: 16, paddingBottom: 20 },
  messageBubble: {
    maxWidth: '82%',
    padding: 14,
    borderRadius: 18,
    marginBottom: 12,
  },
  userBubble: {
    alignSelf: 'flex-end',
    backgroundColor: colors.primary,
    borderBottomRightRadius: 4,
  },
  botBubble: {
    alignSelf: 'flex-start',
    backgroundColor: '#FFFFFF',
    borderBottomLeftRadius: 4,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 1,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 3,
  },
  messageText: { fontSize: 14, lineHeight: 20 },
  userText: { color: '#FFFFFF', fontWeight: '600' },
  botText: { color: colors.textDark, fontWeight: '500' },

  typingIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingBottom: 10,
  },
  typingText: {
    color: colors.textMuted,
    fontStyle: 'italic',
    fontSize: 12,
    fontWeight: '600',
  },

  inputArea: {
    flexDirection: 'row',
    padding: 12,
    paddingBottom: Platform.OS === 'ios' ? 24 : 12,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: colors.border,
    alignItems: 'center',
  },
  input: {
    flex: 1,
    backgroundColor: colors.surfaceAlt,
    borderRadius: 22,
    paddingHorizontal: 18,
    paddingVertical: 10,
    fontSize: 14,
    color: colors.textDark,
    fontWeight: '500',
    marginRight: 10,
  },
  sendButton: {
    backgroundColor: colors.primary,
    width: 44,
    height: 44,
    borderRadius: 22,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
  },
});
