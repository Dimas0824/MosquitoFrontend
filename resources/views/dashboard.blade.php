<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Jentik</title>
    <!-- Tailwind CSS untuk Styling Cepat & Modern -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- React & ReactDOM untuk Logika Interaktif -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <!-- Babel untuk compile JSX di browser -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <!-- Chart.js untuk Grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-chartjs-2@5.2.0/dist/index.umd.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
        }

        .glass-panel {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .animate-fade-in {
            animation: fadeIn 0.2s ease-out;
        }

        .animate-scale-in {
            animation: scaleIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Custom Scrollbar for Gallery */
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body>
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect } = React;

        // --- Ikon SVG Sederhana (Lucide Style) ---
        const IconUser = () => <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>;
        const IconLock = () => <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>;
        const IconEye = () => <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>;
        const IconEyeOff = () => <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>;
        const IconBug = () => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m8 2 1.88 1.88"/><path d="M14.12 3.88 16 2"/><path d="M9 7.13v-1a3.003 3.003 0 1 1 6 0v1"/><path d="M12 20c-3.3 0-6-2.7-6-6v-3a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v3c0 3.3-2.7 6-6 6"/><path d="M12 20v-9"/><path d="M6.53 9C4.6 8.8 3 7.1 3 5"/><path d="M6 13H2"/><path d="M3 21c0-2.1 1.7-3.9 3.8-4"/><path d="M20.97 5c0 2.1-1.6 3.8-3.5 4"/><path d="M22 13h-4"/><path d="M17.2 17c2.1.1 3.8 1.9 3.8 4"/></svg>;
        const IconActivity = () => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>;
        const IconPower = () => <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" x2="12" y1="2" y2="12"/></svg>;
        const IconLogOut = () => <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>;
        const IconGrid = () => <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>;
        const IconList = () => <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>;
        const IconImage = () => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>;
        const IconX = () => <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>;
        const IconChevronRight = () => <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 18 6-6-6-6"/></svg>;

        // --- Komponen Grafik Sederhana (Manual SVG untuk performa ringan) ---
        const SimpleChart = () => {
            // Data dummy: 7 hari terakhir
            const data = [12, 19, 3, 5, 2, 3, 10];
            const max = Math.max(...data);
            const points = data.map((val, i) => {
                const x = (i / (data.length - 1)) * 100;
                const y = 100 - ((val / max) * 100);
                return `${x},${y}`;
            }).join(' ');

            return (
                <div className="w-full h-48 relative mt-4">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" className="w-full h-full overflow-visible">
                        {/* Grid Lines */}
                        <line x1="0" y1="0" x2="100" y2="0" stroke="#e2e8f0" strokeWidth="0.5" />
                        <line x1="0" y1="50" x2="100" y2="50" stroke="#e2e8f0" strokeWidth="0.5" />
                        <line x1="0" y1="100" x2="100" y2="100" stroke="#e2e8f0" strokeWidth="0.5" />

                        {/* Chart Line */}
                        <polyline
                            fill="none"
                            stroke="#3b82f6"
                            strokeWidth="2"
                            points={points}
                            vectorEffect="non-scaling-stroke"
                        />
                        {/* Area under curve */}
                        <polygon
                            fill="rgba(59, 130, 246, 0.1)"
                            points={`0,100 ${points} 100,100`}
                        />
                        {/* Dots */}
                        {data.map((val, i) => (
                            <circle
                                key={i}
                                cx={(i / (data.length - 1)) * 100}
                                cy={100 - ((val / max) * 100)}
                                r="1.5"
                                fill="#2563eb"
                                vectorEffect="non-scaling-stroke"
                            />
                        ))}
                    </svg>
                    <div className="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                    </div>
                </div>
            );
        };

        // --- Komponen Modal Konfirmasi ---
        const Modal = ({ isOpen, onClose, onConfirm, title, message, isProcessing }) => {
            if (!isOpen) return null;
            return (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 animate-fade-in backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 transform transition-all scale-100 animate-scale-in">
                        <div className="text-center">
                            <div className="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <IconPower />
                            </div>
                            <h3 className="text-lg leading-6 font-semibold text-gray-900">{title}</h3>
                            <p className="text-sm text-gray-500 mt-2">{message}</p>
                        </div>
                        <div className="mt-6 flex gap-3">
                            <button
                                onClick={onClose}
                                disabled={isProcessing}
                                className="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                onClick={onConfirm}
                                disabled={isProcessing}
                                className="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm transition-colors flex justify-center items-center"
                            >
                                {isProcessing ? (
                                    <span className="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                                ) : null}
                                {isProcessing ? 'Memproses...' : 'Ya, Aktifkan'}
                            </button>
                        </div>
                    </div>
                </div>
            );
        };

        // --- Komponen Modal Penampil Gambar (Lightbox) ---
        const ImageModal = ({ image, onClose }) => {
            if (!image) return null;
            return (
                <div className="fixed inset-0 bg-black bg-opacity-90 z-[60] flex items-center justify-center p-4 animate-fade-in backdrop-blur-sm" onClick={onClose}>
                    <button onClick={onClose} className="absolute top-4 right-4 text-white hover:text-gray-300 p-2">
                        <IconX />
                    </button>
                    <div className="max-w-4xl w-full max-h-[90vh] flex flex-col items-center" onClick={(e) => e.stopPropagation()}>
                        <div className="relative w-full aspect-video bg-gray-800 rounded-xl overflow-hidden shadow-2xl flex items-center justify-center">
                            {/* Simulasi Gambar */}
                            <div className="w-full h-full bg-slate-800 flex flex-col items-center justify-center text-slate-500">
                                <IconImage />
                                <span className="mt-2 text-sm">Simulasi Gambar: {image.id}</span>
                            </div>

                            {/* Overlay Info */}
                            <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                                <div className="flex justify-between items-end">
                                    <div>
                                        <p className="text-sm opacity-75">{image.date} • {image.time}</p>
                                        <h3 className="text-xl font-bold mt-1">
                                            {image.count} Jentik Terdeteksi
                                        </h3>
                                    </div>
                                    <span className={`px-3 py-1 rounded-full text-sm font-medium ${
                                        image.status === 'Bahaya' ? 'bg-red-600 text-white' :
                                        image.status === 'Waspada' ? 'bg-orange-500 text-white' :
                                        'bg-green-600 text-white'
                                    }`}>
                                        {image.status}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        };

        // --- Halaman Login ---
        const LoginScreen = ({ onLogin }) => {
            const [deviceId, setDeviceId] = useState('');
            const [password, setPassword] = useState('');
            const [showPass, setShowPass] = useState(false);
            const [error, setError] = useState('');
            const [loading, setLoading] = useState(false);

            const handleSubmit = (e) => {
                e.preventDefault();
                setLoading(true);
                setError('');

                // Simulasi delay jaringan
                setTimeout(() => {
                    if (deviceId && password) {
                        onLogin(deviceId);
                    } else {
                        setError('ID Perangkat dan Kata Sandi wajib diisi.');
                        setLoading(false);
                    }
                }, 1000);
            };

            return (
                <div className="min-h-screen flex items-center justify-center p-4 bg-slate-50">
                    <div className="max-w-md w-full glass-panel rounded-2xl p-8">
                        <div className="text-center mb-8">
                            <div className="bg-blue-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                                <IconBug />
                            </div>
                            <h2 className="text-2xl font-bold text-slate-800">Monitoring Jentik</h2>
                            <p className="text-slate-500 text-sm mt-1">Masuk untuk memantau perangkat IoT</p>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">ID Perangkat</label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <IconUser />
                                    </div>
                                    <input
                                        type="text"
                                        value={deviceId}
                                        onChange={(e) => setDeviceId(e.target.value)}
                                        className="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                        placeholder="Contoh: ESP32_TOREN_01"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <IconLock />
                                    </div>
                                    <input
                                        type={showPass ? "text" : "password"}
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        className="block w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                        placeholder="••••••••"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPass(!showPass)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                    >
                                        {showPass ? <IconEyeOff /> : <IconEye />}
                                    </button>
                                </div>
                            </div>

                            {error && (
                                <div className="bg-red-50 text-red-600 text-sm p-3 rounded-lg flex items-center animate-pulse">
                                    <svg className="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" /></svg>
                                    {error}
                                </div>
                            )}

                            <button
                                type="submit"
                                disabled={loading}
                                className="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-95 disabled:opacity-70 flex justify-center items-center"
                            >
                                {loading ? 'Memverifikasi...' : 'Masuk Dashboard'}
                            </button>
                        </form>
                    </div>
                </div>
            );
        };

        // --- Halaman Dashboard ---
        const Dashboard = ({ user, onLogout }) => {
            const [showModal, setShowModal] = useState(false);
            const [isActivating, setIsActivating] = useState(false);
            const [successMsg, setSuccessMsg] = useState('');
            const [selectedImage, setSelectedImage] = useState(null);

            // Data Dummy Riwayat (Simulasi Backend)
            const historyData = [
                { id: 1, time: '10:30 WIB', date: 'Hari Ini', count: 5, status: 'Bahaya' },
                { id: 2, time: '09:00 WIB', date: 'Hari Ini', count: 0, status: 'Aman' },
                { id: 3, time: '16:45 WIB', date: 'Kemarin', count: 2, status: 'Waspada' },
                { id: 4, time: '12:00 WIB', date: 'Kemarin', count: 0, status: 'Aman' },
                { id: 5, time: '10:00 WIB', date: 'Kemarin', count: 8, status: 'Bahaya' },
                { id: 6, time: '08:00 WIB', date: 'Kemarin', count: 1, status: 'Waspada' },
            ];

            const handleActuator = () => {
                setIsActivating(true);
                // Simulasi API Call ke ESP32
                setTimeout(() => {
                    setIsActivating(false);
                    setShowModal(false);
                    setSuccessMsg('Perintah berhasil dikirim ke perangkat!');
                    setTimeout(() => setSuccessMsg(''), 4000);
                }, 2000);
            };

            return (
                <div className="min-h-screen bg-slate-50 pb-20">
                    {/* Header */}
                    <header className="bg-white shadow-sm sticky top-0 z-30">
                        <div className="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                            <div className="flex items-center gap-3">
                                <div className="bg-blue-100 p-2 rounded-lg text-blue-600">
                                    <IconBug />
                                </div>
                                <div>
                                    <h1 className="text-lg font-bold text-slate-800 leading-tight">Dashboard Jentik</h1>
                                    <p className="text-xs text-slate-500">{user}</p>
                                </div>
                            </div>
                            <button onClick={onLogout} className="text-slate-400 hover:text-red-500 transition-colors p-2" title="Keluar">
                                <IconLogOut />
                            </button>
                        </div>
                    </header>

                    <main className="max-w-5xl mx-auto px-4 py-6 space-y-6">

                        {/* Notifikasi Sukses */}
                        {successMsg && (
                            <div className="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center shadow-sm animate-bounce-in">
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg>
                                {successMsg}
                            </div>
                        )}

                        {/* KPI Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Card 1 */}
                            <div className="glass-panel p-5 rounded-2xl flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-slate-500 mb-1">Jentik Terdeteksi (Sesi Terakhir)</p>
                                    <h3 className="text-4xl font-bold text-red-500">5 <span className="text-lg text-slate-400 font-normal">ekor</span></h3>
                                    <p className="text-xs text-red-600 mt-2 font-medium bg-red-50 inline-block px-2 py-1 rounded">⚠️ Status Waspada</p>
                                </div>
                                <div className="bg-red-50 p-3 rounded-full text-red-500">
                                    <IconActivity />
                                </div>
                            </div>

                            {/* Card 2 */}
                            <div className="glass-panel p-5 rounded-2xl flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-slate-500 mb-1">Total Deteksi Hari Ini</p>
                                    <h3 className="text-4xl font-bold text-slate-700">24 <span className="text-lg text-slate-400 font-normal">kali</span></h3>
                                    <p className="text-xs text-green-600 mt-2 font-medium bg-green-50 inline-block px-2 py-1 rounded">✅ Sistem Aktif</p>
                                </div>
                                <div className="bg-blue-50 p-3 rounded-full text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                                </div>
                            </div>
                        </div>

                        {/* Chart & Actuator Row */}
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                            {/* Chart Section */}
                            <div className="glass-panel p-5 rounded-2xl lg:col-span-2">
                                <div className="flex justify-between items-center mb-2">
                                    <h3 className="font-semibold text-slate-700">Tren Mingguan</h3>
                                    <span className="text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded-full">7 Hari Terakhir</span>
                                </div>
                                <SimpleChart />
                            </div>

                            {/* Actuator Control Section */}
                            <div className="glass-panel p-5 rounded-2xl flex flex-col justify-center items-center text-center space-y-4 bg-gradient-to-b from-white to-red-50/30">
                                <div className="bg-white p-4 rounded-full shadow-md text-red-500 mb-2">
                                    <IconPower />
                                </div>
                                <div>
                                    <h3 className="font-semibold text-slate-800">Kontrol Manual</h3>
                                    <p className="text-sm text-slate-500 px-4">Aktifkan pompa/larvasida secara paksa jika deteksi otomatis gagal.</p>
                                </div>
                                <button
                                    onClick={() => setShowModal(true)}
                                    className="w-full bg-white border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-sm active:scale-95"
                                >
                                    Basmi Manual
                                </button>
                            </div>
                        </div>

                        {/* SECTION 1: PHOTO GALLERY (HORIZONTAL SCROLL) */}
                        <div className="glass-panel rounded-2xl overflow-hidden">
                            <div className="p-5 border-b border-slate-100">
                                <div className="flex justify-between items-center">
                                    <div>
                                        <h3 className="font-semibold text-slate-700">Galeri Foto Deteksi</h3>
                                        <p className="text-xs text-slate-400 mt-1">Hasil tangkapan kamera terkini.</p>
                                    </div>
                                    <span className="text-slate-400 text-xs flex items-center">
                                        Geser <IconChevronRight />
                                    </span>
                                </div>
                            </div>

                            {/* Horizontal Scroll Gallery */}
                            <div className="p-5 bg-slate-50/30">
                                <div className="flex overflow-x-auto gap-4 pb-4 animate-fade-in custom-scrollbar">
                                    {historyData.map((item) => (
                                        <div
                                            key={item.id}
                                            onClick={() => setSelectedImage(item)}
                                            className="flex-none w-48 group bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-all cursor-pointer relative"
                                        >
                                            {/* Simulated Image Thumbnail */}
                                            <div className="aspect-square bg-slate-100 flex items-center justify-center text-slate-300 group-hover:bg-slate-200 transition-colors relative">
                                                <div className="absolute inset-0 flex flex-col items-center justify-center">
                                                    <IconImage />
                                                    <span className="text-xs mt-1 font-medium">Foto #{item.id}</span>
                                                </div>

                                                {/* Count Badge on Image */}
                                                <div className="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-0.5 rounded-md text-xs font-bold shadow-sm">
                                                    {item.count} Jentik
                                                </div>
                                            </div>

                                            <div className="p-3">
                                                <div className="flex justify-between items-start mb-1">
                                                    <span className={`text-[10px] font-bold uppercase tracking-wider ${
                                                        item.status === 'Bahaya' ? 'text-red-600' :
                                                        item.status === 'Waspada' ? 'text-orange-500' :
                                                        'text-green-600'
                                                    }`}>
                                                        {item.status}
                                                    </span>
                                                    <span className="text-xs text-slate-400">{item.time}</span>
                                                </div>
                                                <p className="text-xs text-slate-500 font-medium truncate">{item.date}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* SECTION 2: HISTORY TABLE CARD (TERPISAH & BERSIH) */}
                        <div className="glass-panel rounded-2xl overflow-hidden">
                            <div className="p-5 border-b border-slate-100 flex justify-between items-center">
                                <div>
                                    <h3 className="font-semibold text-slate-700">Log Data Riwayat</h3>
                                    <p className="text-xs text-slate-400 mt-1">Catatan tekstual seluruh aktivitas deteksi.</p>
                                </div>
                                <button className="text-xs text-blue-600 font-medium hover:underline">Download CSV</button>
                            </div>

                            <div className="overflow-x-auto animate-fade-in">
                                <table className="w-full text-left text-sm text-slate-600">
                                    <thead className="bg-slate-50 text-slate-500 font-medium uppercase text-xs">
                                        <tr>
                                            <th className="px-5 py-3">Waktu</th>
                                            <th className="px-5 py-3">Lokasi</th>
                                            <th className="px-5 py-3 text-center">Jumlah</th>
                                            <th className="px-5 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 bg-white">
                                        {historyData.map((item) => (
                                            <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                                <td className="px-5 py-3">
                                                    <div className="font-medium text-slate-800">{item.time}</div>
                                                    <div className="text-xs text-slate-400">{item.date}</div>
                                                </td>
                                                <td className="px-5 py-3">{user}</td>
                                                <td className="px-5 py-3 text-center">
                                                    <span className={`font-bold ${item.count > 0 ? 'text-red-600' : 'text-slate-400'}`}>
                                                        {item.count}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-3">
                                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                                        item.status === 'Bahaya' ? 'bg-red-100 text-red-700' :
                                                        item.status === 'Waspada' ? 'bg-orange-100 text-orange-700' :
                                                        'bg-green-100 text-green-700'
                                                    }`}>
                                                        {item.status}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Pagination */}
                            <div className="p-4 border-t border-slate-100 flex justify-between items-center bg-white">
                                <button className="text-slate-400 text-sm hover:text-slate-600 disabled:opacity-50" disabled>← Sebelumnya</button>
                                <span className="text-xs text-slate-400">Hal 1 dari 5</span>
                                <button className="text-blue-600 text-sm hover:text-blue-700 font-medium">Selanjutnya →</button>
                            </div>
                        </div>
                    </main>

                    {/* Modal Confirm */}
                    <Modal
                        isOpen={showModal}
                        onClose={() => setShowModal(false)}
                        onConfirm={handleActuator}
                        title="Konfirmasi Aktivasi"
                        message={`Apakah Anda yakin ingin mengaktifkan aktuator pada perangkat ${user}? Aksi ini tidak dapat dibatalkan.`}
                        isProcessing={isActivating}
                    />

                    {/* Image Viewer Modal */}
                    <ImageModal
                        image={selectedImage}
                        onClose={() => setSelectedImage(null)}
                    />
                </div>
            );
        };

        // --- App Component Utama ---
        const App = () => {
            const [user, setUser] = useState(localStorage.getItem('larva_user') || null);

            const handleLogin = (deviceId) => {
                setUser(deviceId);
                localStorage.setItem('larva_user', deviceId);
            };

            const handleLogout = () => {
                setUser(null);
                localStorage.removeItem('larva_user');
            };

            return (
                <React.Fragment>
                    {user ? <Dashboard user={user} onLogout={handleLogout} /> : <LoginScreen onLogin={handleLogin} />}
                </React.Fragment>
            );
        };

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>

</html>
