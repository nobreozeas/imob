import {
    LayoutDashboard,
    Building2,
    Users,
    UserCog,
    Shield,
    FileText,
    DollarSign,
    Receipt,
    ArrowLeftRight,
    TrendingUp,
    AlertTriangle,
    Tags,
    Settings,
    type LucideIcon,
} from 'lucide-vue-next';

export interface NavItem {
    label: string;
    icon: LucideIcon;
    routeName: string;
    href: string;
    permission?: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export const NAV_GROUPS: NavGroup[] = [
    {
        title: 'Geral',
        items: [
            {
                label: 'Dashboard',
                icon: LayoutDashboard,
                routeName: 'dashboard',
                href: '/dashboard',
            },
        ],
    },
    {
        title: 'Gestão',
        items: [
            {
                label: 'Imóveis',
                icon: Building2,
                routeName: 'imoveis.index',
                href: '/imoveis',
            },
            {
                label: 'Clientes',
                icon: Users,
                routeName: 'clientes.index',
                href: '/clientes',
            },
            {
                label: 'Contratos',
                icon: FileText,
                routeName: 'contratos.index',
                href: '/contratos',
            },
        ],
    },
    {
        title: 'Financeiro',
        items: [
            {
                label: 'Dashboard',
                icon: DollarSign,
                routeName: 'financeiro.dashboard',
                href: '/financeiro/dashboard',
                permission: 'financeiro.visualizar',
            },
            {
                label: 'Lançamentos',
                icon: Receipt,
                routeName: 'financeiro.lancamentos.index',
                href: '/financeiro/lancamentos',
                permission: 'financeiro.visualizar',
            },
            {
                label: 'Repasses',
                icon: ArrowLeftRight,
                routeName: 'financeiro.repasses.index',
                href: '/financeiro/repasses',
                permission: 'repasses.visualizar',
            },
            {
                label: 'Fluxo de Caixa',
                icon: TrendingUp,
                routeName: 'financeiro.fluxo-caixa',
                href: '/financeiro/fluxo-caixa',
                permission: 'financeiro.visualizar',
            },
            {
                label: 'Inadimplência',
                icon: AlertTriangle,
                routeName: 'financeiro.inadimplencia',
                href: '/financeiro/inadimplencia',
                permission: 'financeiro.visualizar',
            },
            {
                label: 'Categorias',
                icon: Tags,
                routeName: 'financeiro.categorias.index',
                href: '/financeiro/categorias',
                permission: 'financeiro.visualizar',
            },
        ],
    },
    {
        title: 'Sistema',
        items: [
            {
                label: 'Usuários',
                icon: UserCog,
                routeName: 'usuarios.index',
                href: '/usuarios',
                permission: 'usuarios.viewAny',
            },
            {
                label: 'Perfis',
                icon: Shield,
                routeName: 'perfis.index',
                href: '/perfis',
                permission: 'perfis.viewAny',
            },
            {
                label: 'Configurações',
                icon: Settings,
                routeName: 'configuracoes.index',
                href: '/configuracoes',
            },
        ],
    },
];

export function useNavigation() {
    return { NAV_GROUPS };
}
