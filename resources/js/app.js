import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.flashModal = function ({ type = 'info', message = '', errors = [] } = {}) {
    const modal = document.getElementById('flash-modal');
    if (!modal || !modal.__x) {
        return;
    }
    modal.__x.$data.open = true;
    modal.__x.$data.type = type;
    modal.__x.$data.message = message;
    modal.__x.$data.errors = errors;
};


document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn_view_job').forEach(button => {
        button.addEventListener('click', function () {

            const jobId = this.dataset.jobId;
            console.log('Job ID:', jobId);

            // modal open
            document.querySelectorAll('.modal_item').forEach(m => {
                m.classList.remove('active');
            });

            const modal = document.querySelector('.modal_item[data-type="modal_view_job"]');
            modal.classList.add('active');

            // AJAX call
            fetch(`/employer/jobs/${jobId}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('jobStatus').innerText = data.status;
                    document.getElementById('jobTitle').innerText = data.title;
                    // document.getElementById('jobType').innerText = data.type;
                    document.getElementById('jobCategory').innerText = data.category;
                    document.getElementById('jobSalary').innerText = data.salary;
                    document.getElementById('jobDeadline').innerText = data.deadline;
                    document.getElementById('posted').innerText = data.created_at;
                    document.getElementById('jobDescription').innerHTML = data.description;
                    document.getElementById('employerName').innerText = data.employer.name;
                     if (data.logo) {
            document.getElementById('jobLogo').src = data.logo;
        } else {
            document.getElementById('jobLogo').src = '/images/default-company.png';
        }
            });
        });
    });

});
