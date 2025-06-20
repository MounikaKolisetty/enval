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
import { PrivacyPolicyComponent } from './privacy-policy/privacy-policy.component';
import { TermsConditionsComponent } from './terms-conditions/terms-conditions.component';
import { CancellationRefundpolicyComponent } from './cancellation-refundpolicy/cancellation-refundpolicy.component';
import { ShippingDeliveryComponent } from './shipping-delivery/shipping-delivery.component';
import { VerifyUserComponent } from './verify-user/verify-user.component';

export const routes: Routes = [
    { path: '', component: HomeComponent , title: 'Enval'},
    { path: 'home', component: HomeComponent, title: 'Enval'},
    { path: 'about', component: AboutComponent, title: 'About - Enval' },
    { path: 'contactus', component: ContactComponent, title: 'Contact - Enval'},
    { path: 'consulting', component: ConsultingComponent, title: 'Consulting - Enval'},
    { path: 'training', component: TrainingComponent, title: 'Trainings - Enval'},
    { path: 'projects', component: ProjectsComponent, title: 'Projects - Enval'},
    { path: 'solutions', component: SolutionsComponent, title: 'Solutions - Enval'},
    { path: 'eventspage-svp', component: EventspageComponent, title: 'Student Value Practitioner (SVP) Certification - Enval'},
    { path: 'eventspage-sva', component: EventspageSvaComponent, title: 'Senior Value Analyst (SVA) Certification - Enval'},
    { path: 'eventspage-pva', component: EventspagePvaComponent, title: 'Professional Value Analyst (PVA) Certification - Enval'},
    { path: 'secure-payment/:courseTitle', component: SecurePaymentComponent, title: 'Payment - Enval'},
    { path: 'training-vma', component: TrainingVmaComponent, title: 'Value Methodology Associate (VMA) Certification - Enval'},
    { path: 'training-vmf2', component:TrainingVmf2Component, title: 'Value Methodology Fundamentals - 2 (VMF2) Certification - Enval'},
    { path: 'training-cvs', component: TrainingCvsComponent, title: 'Certified Value Specialist (CVS) Certification - Enval'},
    { path: 'login', component: RegisterComponent, title: 'Login | Signup - Enval'},
    { path: 'password-reset', component:PasswordResetComponent, title: 'Password Rest - Enval'},
    { path: 'enrolled-courses', component:EnrolledcoursesComponent, title: 'Enrolled Courses - Enval'},
    { path: 'events', component:EventsComponent, title: 'Events - Enval'},
    { path: 'corporate-trainings', component: CorporateTrainingsComponent, title: 'Corporate Trainings - Enval'},
    { path: 'privacypolicy', component: PrivacyPolicyComponent, title: 'Privacy Policy - Enval'},
    { path: 'termsconditions', component: TermsConditionsComponent, title: 'Terms and Conditions - Enval'},
    { path: 'cancellationrefundpolicy', component: CancellationRefundpolicyComponent, title: 'Cancellation and Refund Policy - Enval'},
    { path: 'shippingdelivery', component: ShippingDeliveryComponent, title: 'Shipping and Delivery - Enval'},
    { path: 'verify-email', component: VerifyUserComponent, title: 'Enval'}
];
