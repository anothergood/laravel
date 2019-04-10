import Home from './components/Home.vue';
import LoginComponent from './components/LoginComponent.vue';
import ChatComponent from './components/ChatComponent.vue';
import PrivateChatComponent from './components/PrivateChatComponent.vue';
import PostComponent from './components/PostComponent.vue';

export const routes = [
    { path: '', redirect: '/private-chat' },
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
            },
            {
                path: '/login',
                component: LoginComponent
            },
            {
                path: '/posts',
                component: PostComponent
            }
        ]
    }
];
