var optionsProfileVisit = {
	annotations: {
		position: 'back'
	},
	dataLabels: {
		enabled:false
	},
	chart: {
		type: 'bar',
		height: 300
	},
	fill: {
		opacity:1
	},
	plotOptions: {
	},
	series: [{
		name: 'sales',
		data: [9,20,30,20,10,20,30,20,10,20,30,20]
	}],
	colors: '#435ebe',
	xaxis: {
		categories: ["Jan","Feb","Mar","Apr","May","Jun","Jul", "Aug","Sep","Oct","Nov","Dec"],
	},
}
let optionsVisitorsProfile  = {
	series: [70, 30],
	labels: ['Male', 'Female'],
	colors: ['#435ebe','#55c6e8'],
	chart: {
		type: 'donut',
		width: '100%',
		height:'350px'
	},
	legend: {
		position: 'bottom'
	},
	plotOptions: {
		pie: {
			donut: {
				size: '30%'
			}
		}
	}
}

var optionsEurope = {
	series: [{
		name: 'series1',
		data: [310, 800, 600, 430, 540, 340, 605, 805,430, 540, 340, 605]
	}],
	chart: {
		height: 80,
		type: 'area',
		toolbar: {
			show:false,
		},
	},
	colors: ['#5350e9'],
	stroke: {
		width: 2,
	},
	grid: {
		show:false,
	},
	dataLabels: {
		enabled: false
	},
	xaxis: {
		type: 'datetime',
		categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z","2018-09-19T07:30:00.000Z","2018-09-19T08:30:00.000Z","2018-09-19T09:30:00.000Z","2018-09-19T10:30:00.000Z","2018-09-19T11:30:00.000Z"],
		axisBorder: {
			show:false
		},
		axisTicks: {
			show:false
		},
		labels: {
			show:false,
		}
	},
	show:false,
	yaxis: {
		labels: {
			show:false,
		},
	},
	tooltip: {
		x: {
			format: 'dd/MM/yy HH:mm'
		},
	},
};

let optionsAmerica = {
	...optionsEurope,
	colors: ['#008b75'],
}
let optionsIndonesia = {
	...optionsEurope,
	colors: ['#dc3545'],
}



var chartProfileVisit = new ApexCharts(document.querySelector("#chart-profile-visit"), optionsProfileVisit);
var chartVisitorsProfile = new ApexCharts(document.getElementById('chart-visitors-profile'), optionsVisitorsProfile)
var chartEurope = new ApexCharts(document.querySelector("#chart-europe"), optionsEurope);
var chartAmerica = new ApexCharts(document.querySelector("#chart-america"), optionsAmerica);
var chartIndonesia = new ApexCharts(document.querySelector("#chart-indonesia"), optionsIndonesia);

chartIndonesia.render();
chartAmerica.render();
chartEurope.render();
chartProfileVisit.render();
chartVisitorsProfile.render()

    document.addEventListener('DOMContentLoaded', function () {
	const inactiveUsersCard = document.getElementById('inactive-users-card');
        const inactiveUsersModal = new bootstrap.Modal(document.getElementById('inactiveUsersModal'));
        const inactiveUsersTableBody = document.getElementById('inactiveUsersTableBody');
    
        inactiveUsersCard.addEventListener('click', function() {
            fetch('/admin/dashboard/getInactiveUsers')
                .then(response => response.json())
                .then(data => {
                    let tableContent = '';
                    data.forEach(user => {
                        tableContent += `
                            <tr>
                                <td>${user.no}</td>
                                <td>${user.email}</td>
                                <td>${user.username}</td>
                                <td>${user.fullname}</td>
                                <td>${user.unit_organisasi}</td>
                                <td>${user.unit_kerja}</td>
                            </tr>
                        `;
                    });
                    inactiveUsersTableBody.innerHTML = tableContent;
                    inactiveUsersModal.show();
                })
                .catch(error => console.error('Error:', error));
        });

	var select = document.getElementById('unitOrganisasiSelect');
        var container = document.getElementById('unitKerjaContainer');
        var table = document.getElementById('unitKerjaTable');
        var tbody = table.getElementsByTagName('tbody')[0];

        select.addEventListener('change', function () {
            var selectedOrg = this.value;
            if (selectedOrg === '') {
                container.style.display = 'none';
                return;
            }

            var unitKerja = unitKerjaData[selectedOrg] || [];

            var unitKerjaWithCount = unitKerja.map(function (unit) {
                return {
                    nama: unit,
                    jumlah: countUnitKerja[unit] || 0
                };
            });

            unitKerjaWithCount.sort(function (a, b) {
                return b.jumlah - a.jumlah;
            });

            tbody.innerHTML = '';

            unitKerjaWithCount.forEach(function (unit, index) {
                var row = tbody.insertRow();
                row.insertCell(0).textContent = index + 1;
                row.insertCell(1).textContent = unit.nama;
                row.insertCell(2).textContent = unit.jumlah;
            });
            container.style.display = 'block';
        });

        var unitOrganisasiOptions = {
            series: [{
				name: 'Jumlah',
				data: Object.values(countUnitOrganisasi)
			}],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
				categories: Object.keys(countUnitOrganisasi),
			},
            yaxis: {
                title: {
                    text: 'Jumlah'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            }
        };
        var unitOrganisasiChart = new ApexCharts(document.querySelector("#unit-organisasi-chart"), unitOrganisasiOptions);
        unitOrganisasiChart.render();

        var userPerMonthOptions = {
            series: [{
				name: 'Users',
				data: Object.values(userPerMonth)
			}],
            chart: {
                type: 'area',
                height: 350
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
				categories: Object.keys(userPerMonth),
			},
            yaxis: {
                title: {
                    text: 'Pengguna'
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            }
        };
        var userPerMonthChart = new ApexCharts(document.querySelector("#user-per-month-chart"), userPerMonthOptions);
        userPerMonthChart.render();

        var urlsPerMonthOptions = {
            series: [{
				name: 'Shortened URLs',
				data: Object.values(urlsPerMonth)
			}],
            chart: {
                type: 'area',
                height: 350
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
				categories: Object.keys(urlsPerMonth),
			},
            yaxis: {
                title: {
                    text: 'Shortlink'
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val;
                    }
                }
            }
        };
        var urlsPerMonthChart = new ApexCharts(document.querySelector("#urls-per-month-chart"), urlsPerMonthOptions);
        urlsPerMonthChart.render();
    
        function createDynamicChart(elementId, data, title) {
            const monthlyData = Object.entries(data).map(([month, details]) => ({
                x: month,
                y: details.total
            }));
        
            const options = {
                series: [{
                    name: title,
                    data: monthlyData
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    type: 'category'
                },
                yaxis: {
                    title: {
                        text: title
                    }
                },
                title: {
                    text: title + ' per Bulan',
                    align: 'center'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val
                        }
                    }
                }
            };
        
            const chart = new ApexCharts(document.querySelector(elementId), options);
            chart.render();
        
            const monthSelector = document.createElement('select');
            monthSelector.id = elementId + '-month-selector';
            Object.keys(data).forEach(month => {
                const option = document.createElement('option');
                option.value = month;
                option.textContent = month;
                monthSelector.appendChild(option);
            });
        
            const weekSelector = document.createElement('select');
            weekSelector.id = elementId + '-week-selector';
            weekSelector.style.display = 'none';
        
            const dateSelector = document.createElement('input');
            dateSelector.type = 'date';
            dateSelector.id = elementId + '-date-selector';
            dateSelector.style.display = 'none';
        
            document.querySelector(elementId).parentNode.insertBefore(monthSelector, document.querySelector(elementId));
            document.querySelector(elementId).parentNode.insertBefore(weekSelector, document.querySelector(elementId));
            document.querySelector(elementId).parentNode.insertBefore(dateSelector, document.querySelector(elementId));
        
            monthSelector.addEventListener('change', function() {
                const selectedMonth = this.value;
                updateWeekSelector(selectedMonth, data, weekSelector);
                updateChartToWeekly(chart, data[selectedMonth].weeks, selectedMonth, title);
                weekSelector.style.display = 'inline';
                dateSelector.style.display = 'inline';
                dateSelector.min = data[selectedMonth].weeks['Week 1'].start;
                dateSelector.max = data[selectedMonth].weeks[`Week ${Object.keys(data[selectedMonth].weeks).length}`].end;
            });
        
            weekSelector.addEventListener('change', function() {
                const selectedMonth = monthSelector.value;
                const selectedWeek = this.value;
                updateChartToSingleWeek(chart, data[selectedMonth].weeks[selectedWeek], selectedMonth, selectedWeek, title);
            });
        
            dateSelector.addEventListener('change', function() {
                const selectedDate = this.value;
                const selectedMonth = monthSelector.value;
                updateChartToSingleDay(chart, data[selectedMonth], selectedDate, title);
            });
        
            return chart;
        }
        
        function updateWeekSelector(month, data, weekSelector) {
            weekSelector.innerHTML = '';
            Object.keys(data[month].weeks).forEach(week => {
                const option = document.createElement('option');
                option.value = week;
                option.textContent = week;
                weekSelector.appendChild(option);
            });
        }
        
        function updateChartToWeekly(chart, weeklyData, month, title) {
            const newData = Object.entries(weeklyData).map(([week, details]) => ({
                x: week,
                y: Object.values(details.data).reduce((a, b) => a + b, 0)
            }));
        
            chart.updateOptions({
                series: [{
                    data: newData
                }],
                title: {
                    text: title + ' pada ' + month
                }
            }, true, true);
        }
        
        function updateChartToSingleWeek(chart, weekData, month, week, title) {
            const dailyData = Object.entries(weekData.data).map(([date, count]) => ({
                x: date,
                y: count
            }));
        
            chart.updateOptions({
                series: [{
                    data: dailyData
                }],
                title: {
                    text: title + ' pada ' + month + ' - ' + week
                }
            }, true, true);
        }
        
        function updateChartToSingleDay(chart, monthData, date, title) {
            const selectedWeek = Object.entries(monthData.weeks).find(([week, data]) => 
                new Date(date) >= new Date(data.start) && new Date(date) <= new Date(data.end)
            );
        
            if (selectedWeek) {
                const count = selectedWeek[1].data[date] || 0;
                chart.updateOptions({
                    series: [{
                        data: [{
                            x: date,
                            y: count
                        }]
                    }],
                    title: {
                        text: title + ' pada ' + date
                    }
                }, true, true);
            }
        }
        const userChart = createDynamicChart("#user-chart", userDataMonthly, "Pengguna");
        const urlChart = createDynamicChart("#url-chart", urlDataMonthly, "Shortlink");
    });
