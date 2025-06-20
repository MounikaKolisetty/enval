import { Component } from '@angular/core';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FooterComponent } from '../footer/footer.component';
import { NavbarComponent } from '../navbar/navbar.component';

@Component({
  selector: 'app-privacy-policy',
  standalone: true,
  imports: [ScrollButtonComponent,
            FooterComponent,
            NavbarComponent
  ],
  templateUrl: './privacy-policy.component.html',
  styleUrl: './privacy-policy.component.css'
})
export class PrivacyPolicyComponent {

}
