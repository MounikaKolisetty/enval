import { Routes } from '@angular/router';
import { AboutComponent } from './about/about.component';
import { HomeComponent } from './home/home.component';
import { ContactComponent } from './contact/contact.component';
import { ConsultingComponent } from './consulting/consulting.component';
import { TrainingComponent } from './training/training.component';
import { ProjectsComponent } from './projects/projects.component';
import { SolutionsComponent } from './solutions/solutions.component';
import { EventspageComponent } from './eventspage/eventspage.component';
import { EventspageSvaComponent } from './eventspage-sva/eventspage-sva.component';
import { EventspagePvaComponent } from './eventspage-pva/eventspage-pva.component';
import { SecurePaymentComponent } from './secure-payment/secure-payment.component';
import { TrainingVmaComponent } from './training-vma/training-vma.component';
import { TrainingVmf2Component } from './training-vmf2/training-vmf2.component';
import { TrainingCvsComponent } from './training-cvs/training-cvs.component';
import { PasswordResetComponent } from './password-reset/password-reset.component';
import { EnrolledcoursesComponent } from './enrolledcourses/enrolledcourses.component';
import { RegisterComponent } from './register/register.component';
import { EventsComponent } from './events/events.component';
import { CorporateTrainingsComponent } from './corporate-trainings/corporate-trainings.component';

export const routes: Routes = [
    { path: '', component: HomeComponent },
    { path: 'home', component: HomeComponent },
    { path: 'about', component: AboutComponent },
    { path: 'contact', component: ContactComponent},
    { path: 'consulting', component: ConsultingComponent},
    { path: 'training', component: TrainingComponent},
    { path: 'projects', component: ProjectsComponent},
    { path: 'solutions', component: SolutionsComponent},
    { path: 'eventspage-svp', component: EventspageComponent},
    { path: 'eventspage-sva', component: EventspageSvaComponent},
    { path: 'eventspage-pva', component: EventspagePvaComponent},
    { path: 'secure-payment', component: SecurePaymentComponent},
    { path: 'training-vma', component: TrainingVmaComponent},
    { path: 'training-vmf2', component:TrainingVmf2Component},
    { path: 'training-cvs', component: TrainingCvsComponent},
    { path: 'login', component: RegisterComponent},
    { path: 'password-reset', component:PasswordResetComponent},
    { path: 'enrolled-courses', component:EnrolledcoursesComponent},
    { path: 'events', component:EventsComponent},
    { path: 'corporate-trainings', component: CorporateTrainingsComponent}
];
