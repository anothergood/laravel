import Home from './components/Home.vue';
import LoginComponent from './components/LoginComponent.vue';
import ChatComponent from './components/ChatComponent.vue';
import PrivateChatComponent from './components/PrivateChatComponent.vue';

export const routes = [
    {
        path: '/home',
        component: Home,
        children: [
            {
                path: '/chat',
                component: ChatComponent
            },
            {
                path: '/private-chat',
                component: PrivateChatComponent
            }
        ]
    },
    {
        path: '/login',
        component: LoginComponent
    }
];
